<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PermitController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\BouwheerController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoodIssueController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\GoodReceiveController;
use App\Http\Controllers\IssuePurposeController;
use App\Http\Controllers\MaterialRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'postLogin']);

Route::get('register', [AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('register', [AuthController::class, 'store']);
Route::post('checkNewEmail', [AuthController::class, 'checkNewEmail'])->name('register.checknewemail');


Route::group(['middleware' => ['auth']], function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/data', [DashboardController::class, 'searchItem'])->name('dashboard.searchitem');
    Route::post('/search', [DashboardController::class, 'search'])->name('dashboard.search');

    Route::get('users/data', [UserController::class, 'getUsers'])->name('users.data');
    Route::post('users/checkNewEmail', [UserController::class, 'checkNewEmail'])->name('users.checknewemail');
    Route::post('users/checkEditEmail/{id}', [UserController::class, 'checkEditEmail'])->name('users.checkeditemail');
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    Route::get('bouwheers/data', [BouwheerController::class, 'getBouwheers'])->name('bouwheers.data');
    Route::post('bouwheers/import', [BouwheerController::class, 'import'])->name('bouwheers.import');
    Route::resource('bouwheers', BouwheerController::class)->except(['create', 'show', 'edit']);

    Route::get('warehouses/data', [WarehouseController::class, 'getWarehouses'])->name('warehouses.data');
    Route::post('warehouses/import', [WarehouseController::class, 'import'])->name('warehouses.import');
    Route::resource('warehouses', WarehouseController::class)->except(['create', 'show', 'edit']);

    Route::get('vendors/data', [VendorController::class, 'getVendors'])->name('vendors.data');
    Route::resource('vendors', VendorController::class)->except(['create', 'show', 'edit']);

    Route::get('groups/data', [GroupController::class, 'getGroups'])->name('groups.data');
    Route::resource('groups', GroupController::class)->except(['create', 'show', 'edit']);

    Route::get('projects/data', [ProjectController::class, 'getProjects'])->name('projects.data');
    Route::post('projects/import', [ProjectController::class, 'import'])->name('projects.import');
    Route::resource('projects', ProjectController::class)->except(['create', 'show', 'edit']);

    Route::get('issuepurposes/data', [IssuePurposeController::class, 'getIssuePurposes'])->name('issuepurposes.data');
    Route::resource('issuepurposes', IssuePurposeController::class)->except(['create', 'show', 'edit']);

    Route::get('items/data', [ItemController::class, 'getItems'])->name('items.data');
    Route::get('items/dataForTransaction', [ItemController::class, 'getItemsForTransaction'])->name('items.dataForTransaction');
    Route::get('items/searchItemByCode', [ItemController::class, 'searchItemByCode'])->name('items.searchItemByCode');
    Route::get('items/getBatch', [ItemController::class, 'getBatchByItem'])->name('items.getbatchbyitem');
    Route::post('items/import', [ItemController::class, 'import'])->name('items.import');
    Route::resource('items', ItemController::class);

    Route::get('goodreceive/data', [GoodReceiveController::class, 'getGoodReceive'])->name('goodreceive.data');
    Route::get('goodreceive/receive-batch', [GoodReceiveController::class, 'receiveBatch'])->name('goodreceive.receivebatch');
    Route::get('goodreceive/forget', [GoodReceiveController::class, 'forget'])->name('goodreceives.forget');
    Route::get('goodreceive/{goodreceive}/print', [GoodReceiveController::class, 'print'])->name('goodreceive.print');
    Route::post('goodreceive/{goodreceive}/cancel', [GoodReceiveController::class, 'cancel'])->name('goodreceive.cancel');
    Route::resource('goodreceive', GoodReceiveController::class);

    Route::get('goodissues/data', [GoodIssueController::class, 'getGoodIssue'])->name('goodissues.data');
    Route::get('goodissues/issue-batch', [GoodIssueController::class, 'issueBatch'])->name('goodissues.issuebatch');
    Route::get('goodissues/forget', [GoodIssueController::class, 'forget'])->name('goodissues.forget');
    Route::get('goodissues/{goodissue}/print', [GoodIssueController::class, 'print'])->name('goodissues.print');
    Route::post('goodissues/{goodissue}/cancel', [GoodIssueController::class, 'cancel'])->name('goodissues.cancel');
    Route::get('goodsissues/create', [GoodIssueController::class, 'create'])->name('goodsissues.create');
    Route::get('goodsissues/get-mr-reference/{id}', [GoodIssueController::class, 'getMrReference'])->name('goodsissues.getmrreference');
    Route::resource('goodissues', GoodIssueController::class);

    Route::get('transfers/data', [TransferController::class, 'getTransfer'])->name('transfers.data');
    Route::get('transfers/transfer-batch', [TransferController::class, 'transferBatch'])->name('transfers.transferbatch');
    Route::get('transfers/{transfer}/print', [TransferController::class, 'print'])->name('transfers.print');
    Route::post('transfers/listWarehouses', [TransferController::class, 'listWarehouses'])->name('transfers.listwarehouses');
    Route::post('transfers/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfers.cancel');
    Route::post('transfers/getTransferReference', [TransferController::class, 'getTransferReference'])->name('transfers.getTransferReference');
    Route::resource('transfers', TransferController::class);

    Route::get('materialrequests/data', [MaterialRequestController::class, 'getMaterialRequest'])->name('materialrequests.data');
    Route::get('materialrequests/request-batch', [MaterialRequestController::class, 'requestBatch'])->name('materialrequests.requestbatch');
    Route::get('materialrequests/forget', [MaterialRequestController::class, 'forget'])->name('materialrequests.forget');
    Route::get('materialrequests/{materialrequest}/print', [MaterialRequestController::class, 'print'])->name('materialrequests.print');
    Route::post('materialrequests/{materialrequest}/cancel', [MaterialRequestController::class, 'cancel'])->name('materialrequests.cancel');
    Route::resource('materialrequests', MaterialRequestController::class);


    Route::post('inventories/checkStock', [InventoryController::class, 'checkStock'])->name('inventories.checkStock');

    // Route::get('batches/data', [BatchController::class, 'getBatch'])->name('batches.data');
    // Route::post('batches/receive', [BatchController::class, 'receive'])->name('batches.receive');
    // Route::post('batches/issue', [BatchController::class, 'issue'])->name('batches.issue');
    // Route::post('batches/issueNonBatch', [BatchController::class, 'issueNonBatch'])->name('batches.issuenonbatch');
    // Route::post('batches/transfer', [BatchController::class, 'transfer'])->name('batches.transfer');
    // Route::post('batches/transferNonBatch', [BatchController::class, 'transferNonBatch'])->name('batches.transfernonbatch');
    // Route::resource('batches', BatchController::class);

    Route::get('reports/good-receive', [ReportController::class, 'goodReceive'])->name('report.goodreceive');
    Route::get('reports/material-request', [ReportController::class, 'materialRequest'])->name('report.materialrequest');
    Route::get('reports/good-issue', [ReportController::class, 'goodIssue'])->name('report.goodissue');
    Route::get('reports/transfer', [ReportController::class, 'transfer'])->name('report.transfer');
    Route::get('reports/inventory-audit', [ReportController::class, 'inventoryAuditReport'])->name('report.inventoryaudit');
    Route::get('reports/inventory-in-warehouse', [ReportController::class, 'inventoryInWarehouse'])->name('report.inventoryinwarehouse');
    // Route::get('reports/permit', [ReportController::class, 'permitReport'])->name('report.permit');

    // Route::get('permits/data', [PermitController::class, 'getPermit'])->name('permits.data');
    // Route::get('permits/extend/{permit}', [PermitController::class, 'extend'])->name('permits.extend');
    // Route::post('permits/extend', [PermitController::class, 'extendSubmit'])->name('permits.extendSubmit');
    // Route::patch('permits/updateItem/{permit_id}/{id}', [PermitController::class, 'updateItem'])->name('permits.updateItem');
    // Route::get('permits/deleteItem/{permit_id}/{id}', [PermitController::class, 'deleteItem'])->name('permits.deleteItem');
    // Route::get('permits/getExtendReference/{id}', [PermitController::class, 'getExtendReference'])->name('permits.getExtendReference');
    // Route::resource('permits', PermitController::class);
});
