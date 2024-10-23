<!-- sidebar menu -->
<div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
  <div class="menu_section">
    <h3>IT Store</h3>
    <ul class="nav side-menu">
      <li class="{{ Request::is('/') || Request::is('search*') ? 'active current-page' : '' }}"><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      @can('admin')
      <li class="{{ Request::is('goodreceive*') || Request::is('goodissues*') || Request::is('transfers*') ? 'active' : '' }}"><a><i class=" fa fa-file-text"></i> Transactions <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu" style="display: {{ Request::is('goodreceive*') || Request::is('goodissues*') || Request::is('transfers*') ? 'block' : 'none' }}">
          <li class="{{ Request::is('goodreceive*') ? 'current-page' : '' }}"><a href="{{ url('goodreceive') }}">Goods Receive</a></li>
          <li class="{{ Request::is('goodissues*') ? 'current-page' : '' }}"><a href="{{ url('goodissues') }}">Goods Issues</a></li>
          <li class="{{ Request::is('transfers*') ? 'current-page' : '' }}"><a href="{{ url('transfers') }}">Inventory Transfer</a></li>
        </ul>
      </li>
      @endcan
      <li class="{{ Request::is('reports*') ? 'active' : '' }}"><a><i class=" fa fa-table"></i> Reports <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu" style="display: {{ Request::is('reports*') ? 'block' : 'none' }}">
          <li class="{{ Request::is('reports/good-receive*') ? 'current-page' : '' }}"><a href="{{ url('reports/good-receive') }}">Good Receive Reports</a></li>
          <li class="{{ Request::is('reports/good-issue*') ? 'current-page' : '' }}"><a href="{{ url('reports/good-issue') }}">Good Issue Reports</a></li>
          <li class="{{ Request::is('reports/transfer*') ? 'current-page' : '' }}"><a href="{{ url('reports/transfer') }}">Transfer Reports</a></li>
          <li class="{{ Request::is('reports/inventory-audit*') ? 'current-page' : '' }}"><a href="{{ url('reports/inventory-audit') }}">Inventory Audit Reports</a></li>
          <li class="{{ Request::is('reports/inventory-in-warehouse*') ? 'current-page' : '' }}"><a href="{{ url('reports/inventory-in-warehouse') }}">Inventory in Warehouse Reports</a></li>
        </ul>
      </li>
      @can('admin')
      <li class="{{ Request::is('items*') || Request::is('groups*') ? 'active' : '' }}"><a><i class="fa fa-database"></i> Item Master Data <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu" style="display: {{ Request::is('items*') || Request::is('groups*') ? 'block' : 'none' }}">
          <li class="{{ Request::is('items*') ? 'current-page' : '' }}"><a href="{{ url('items') }}"> Items</a></li>
          <li><a href="{{ url('groups') }}"> Groups</a></li>
          <li><a href="{{ url('issuepurposes') }}"> Issue Purposes</a></li>
        </ul>
      </li>
      <li><a><i class="fa fa-database"></i> Business Master Data <span class="fa fa-chevron-down"></span></a>
        <ul class="nav child_menu">
          <li><a href="{{ url('warehouses') }}"> Warehouses</a></li>
          <li><a href="{{ url('bouwheers') }}"> Bouwheers</a></li>
          <li><a href="{{ url('projects') }}"> Projects</a></li>
          <li><a href="{{ url('vendors') }}"> Vendors</a></li>
        </ul>
      </li>
      <li><a href="{{ url('users') }}"><i class="fa fa-users"></i> Users</a></li>
      @endcan
    </ul>
  </div>
</div>
<!-- /sidebar menu -->

<!-- /menu footer buttons -->
<div class="sidebar-footer hidden-small">
  {{-- <a data-toggle="tooltip" data-placement="top" title="Settings">
    <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
  </a>
  <a data-toggle="tooltip" data-placement="top" title="FullScreen">
    <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
  </a>
  <a data-toggle="tooltip" data-placement="top" title="Lock">
    <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
  </a>
  <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
  </a> --}}
</div>
<!-- /menu footer buttons -->
</div>
</div>
