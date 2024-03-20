$(document).ready(function () {
    $(".select2").select2();

    $(document).on("select2:open", () => {
        document.querySelector(".select2-search__field").focus();
    });

    // Variabel untuk melacak nomor baris
    addItemDetail(1); // Tambahkan baris pertama onload
    var rowCount = 2; // untuk row berikutnya saat di klik

    // Fungsi untuk menambahkan baris
    $("#dynamic-ar").on("click", function () {
        addItemDetail(rowCount);
        rowCount++; // Tingkatkan nomor baris setiap kali menambahkan baris
    });

    function addItemDetail(rowNumber) {
        var type = window.dataType; // Mengambil data-type dari variabel global
        var tr = `<tr>
                    <td>
                    <div class="input-group">
                        <input type="hidden" class="form-control item-id-${rowNumber}" name="item_id[${rowNumber}]" placeholder="${rowNumber}" required>
                        <input type="text" class="form-control item-code-${rowNumber}" name="item_code[${rowNumber}]" required>
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-primary search-item-${rowNumber}"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                    </td>
                    <td><input type="text" class="form-control description-${rowNumber}" readonly></td>
                    <td><input type="number" class="form-control ${type}-qty-${rowNumber}" name="${type}_qty[${rowNumber}]" required data-parsley-min="1" data-parsley-remote="http://localhost/bh-inventory/inventories/checkStock"></td>
                    <td><input type="text" class="form-control ${type}-line-remarks" name="${type}_line_remarks[${rowNumber}]" required></td>
                    <td><button type="button" class="btn btn-danger remove-input-field"><i class="fa fa-times"></i></button></td>
                </tr>`;
        $("#inputTable").append(tr);

        // Inisialisasi autocomplete pada elemen "item-code" dalam baris baru
        var newRowItemCode = $(`#inputTable tr:last .item-code-${rowNumber}`);
        initializeAutocomplete(newRowItemCode, rowNumber);

        // Menambahkan event handler untuk mengatur sumber data saat input berubah
        $(document).on("input", `.item-code-${rowNumber}`, function () {
            var inputElement = $(this);
            var inputText = inputElement.val();

            // Temukan elemen item yang sesuai dalam baris yang sama
            var itemIDElement = inputElement.siblings(`.item-id-${rowNumber}`);
            var itemCodeElement = inputElement.siblings(
                `.item-code-${rowNumber}`
            );
            var descriptionElement = inputElement.siblings(
                `.description-${rowNumber}`
            );

            // Lakukan AJAX request untuk mencari item berdasarkan inputText
            $.ajax({
                // url: "{{ route('items.searchItemByCode') }}"
                url: "http://localhost/bh-inventory/items/searchItemByCode",
                type: "get",
                dataType: "json",
                data: {
                    item_code: inputText,
                },
                success: function (data) {
                    if (data) {
                        var items = [];
                        for (var i = 0; i < data.length; i++) {
                            items.push({
                                id: data[i].id,
                                label: data[i].item_code,
                                desc: data[i].description,
                            });
                        }
                        // console.log(items);

                        // Setel sumber data autocomplete untuk elemen yang sesuai dalam baris yang sama
                        initializeAutocomplete(inputElement, rowNumber);
                        inputElement.autocomplete("option", "source", items);

                        // Setel nilai item-id dan description yang sesuai dalam baris yang berbeda
                        itemIDElement.val(data[0].id);
                        itemCodeElement.val(data[0].label);
                        descriptionElement.val(data[0].description);
                    } else {
                        // Jika item tidak ditemukan, kosongkan sumber data autocomplete
                        inputElement.autocomplete("option", "source", []);

                        // Kosongkan nilai item-id dan description dalam baris yang sama
                        itemIDElement.val("");
                        itemCodeElement.val("");
                        descriptionElement.val("");
                    }
                },
            });
        });

        // Tambahkan event handler untuk menghapus baris
        $(document).on("click", ".remove-input-field", function () {
            $(this).parents("tr").remove();
            updateRowNumbers();
        });

        // Fungsi untuk mengupdate nomor baris
        function updateRowNumbers() {
            var type = window.dataType; // Mengambil data-type dari variabel global
            var newRowNumber = 1;
            $("#inputTable tr").each(function () {
                $(this)
                    .find(`.item-id`)
                    .attr("name", `item_id[${newRowNumber}]`);
                $(this)
                    .find(`.item-code`)
                    .attr("name", `item_code[${newRowNumber}]`);
                $(this)
                    .find(`.${type}-qty`)
                    .attr("name", `${type}_qty[${newRowNumber}]`);
                $(this)
                    .find(`.${type}-line-remarks`)
                    .attr("name", `${type}_line_remarks[${newRowNumber}]`);
                newRowNumber++;
            });
        }

        // Menambahkan event handler untuk tombol .search-item-${rowNumber} yang memunculkan modal #itemModal sesuai nomor urut
        $(document).on("click", `.search-item-${rowNumber}`, function () {
            $("#itemModal").modal("show");
            listItem(rowNumber);
        });

        // Handle item selection in the modal and update the corresponding row
        $("#datatable-serverside").on(
            "click",
            `button.pick-item-${rowNumber}`,
            function () {
                var itemID = $(this).data("item-id");
                var itemCode = $(this).data("item-code");
                var description = $(this).data("description");

                // Temukan baris yang terkait dengan tombol "Cari" yang diklik
                // console.log('rowNumber: ' + rowNumber);
                // console.log('itemID: ' + itemID);
                // console.log('itemCode: ' + itemCode);
                // console.log('description: ' + description);

                // Update nilai item-id, item-code, dan description
                $(`.item-id-${rowNumber}`).val(itemID);
                $(`.item-code-${rowNumber}`).val(itemCode);
                $(`.description-${rowNumber}`).val(description);

                // Sembunyikan modal setelah memilih item
                $("#itemModal").modal("hide");
                $(`.item-code-${rowNumber}`).focus();
            }
        );

        if (type == "gi") {
            var form = $("#gi").parsley();

            form.on("field:validate", function () {
                // Logika saat validasi gagal
                if (form.isValid()) {
                    var warehouse_id = $("#warehouse").val(); // Ganti dengan ID input warehouse
                    var item_id = $(`.item-id-${rowNumber}`).val(); // Ganti dengan ID input item_id
                    var qty = $(`.${type}-qty-${rowNumber}`).val(); // Ganti dengan class qty yang sesuai
                    var url = form.$element.data("parsley-remote"); // Dapatkan URL dari atribut data-parsley-remote

                    console.log(warehouse_id);
                    console.log(item_id);
                    console.log(qty);
                    console.log(url);

                    $.ajax({
                        url: url,
                        type: "GET",
                        data: {
                            item_id: item_id,
                            warehouse_id: warehouse_id,
                        },
                        success: function (response) {
                            if (response.valid) {
                                // Stok cukup, Anda bisa melanjutkan proses
                            } else {
                                // Stok tidak cukup, tampilkan pesan kesalahan
                                $(".${type}-qty-${rowNumber}")
                                    .parsley()
                                    .addError("data-parsley-remote", {
                                        message: "Stok tidak cukup.",
                                    });
                            }
                        },
                    });
                }
            });
        }
    }

    // Fungsi autocomplete yang dapat digunakan kembali
    function initializeAutocomplete(elements, rowNumber) {
        elements
            .autocomplete({
                minLength: 0,
                source: [],
                focus: function (event, ui) {
                    elements.val(ui.item.label);
                    return false;
                },
                select: function (event, ui) {
                    var itemIDElement = elements.siblings(
                        `.item-id-${rowNumber}`
                    );
                    var itemCodeElement = elements.siblings(
                        `.item-code-${rowNumber}`
                    );
                    var descriptionElement = elements.siblings(
                        `.description-${rowNumber}`
                    );

                    // Setel nilai .description dalam <td> yang berbeda
                    var descriptionElementInRow = elements
                        .closest("tr")
                        .find(`.description-${rowNumber}`);
                    descriptionElementInRow.val(ui.item.desc);

                    itemIDElement.val(ui.item.id);
                    itemCodeElement.val(ui.item.label);
                    descriptionElement.val(ui.item.desc);

                    return false;
                },
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .addClass("autocomplete-item")
                .append("<div>" + item.label + "<br>" + item.desc + "</div")
                .appendTo(ul);
        };
    }

    // datatable serverside list item
    function listItem(rowNumber) {
        var table = $("#datatable-serverside").DataTable({
            responsive: true,
            autoWidth: true,
            lengthChange: true,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ["10", "25", "50", "100", "Show all"],
            ],
            dom: "lfrtpi",
            processing: true,
            serverSide: true,
            ajax: {
                // url: "{{ route('items.dataForTransaction') }}"
                url: "http://localhost/bh-inventory/items/dataForTransaction",
                data: function (d) {
                    d.search = $(
                        "input[type=search][aria-controls=datatable-serverside]"
                    ).val();
                    // console.log(d);
                },
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                },
                {
                    data: "type_name",
                    name: "type_name",
                    orderable: false,
                },
                {
                    data: "group_name",
                    name: "group_name",
                    orderable: false,
                },
                {
                    data: "item_code",
                    name: "item_code",
                    orderable: false,
                },
                {
                    data: "description",
                    name: "description",
                    orderable: false,
                },
                {
                    data: "item_status",
                    name: "item_status",
                    orderable: false,
                    className: "text-center",
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (data, type, row, meta) {
                        var itemId = row.id;
                        var itemCode = row.item_code;
                        var description = row.description;

                        return `<button class="btn btn-sm btn-info pick-item-${rowNumber}" data-item-id="${itemId}" data-item-code="${itemCode}" data-description="${description}"><i class="fa fa-check-square-o"></i> Pick!</button>`;
                    },
                },
            ],
            fixedColumns: true,
            destroy: true, // agar tidak reinitialize setiap kali listItem dipanggil
        });
    }

    function validateStock(itemId, warehouseId, rowNumber) {
        window.Parsley.addValidator(`checkstock-${rowNumber}`, {
            validateString: function (value) {
                return $.ajax({
                    url: "http://localhost/bh-inventory/inventories/checkStock",
                    method: "GET",
                    dataType: "json",
                    data: {
                        item_id: itemId,
                        warehouse_id: warehouseId,
                    },
                    success: function (data) {
                        if (data) {
                            return true;
                        } else {
                            return false;
                        }
                    },
                });
            },
            messages: {
                en: "This email is already registered.", // Pesan jika validasi gagal
            },
        });
    }
});
