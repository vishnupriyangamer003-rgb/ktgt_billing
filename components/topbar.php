<?php $__topbarUser = current_user(); ?>
<header class="topbar">
  <button class="topbar-toggle" id="mobileSidebarToggle" title="Menu">
    <i class="bi bi-list fs-4"></i>
  </button>

  <div class="global-search">
    <i class="bi bi-search"></i>
    <input type="text" id="globalSearchInput" placeholder="Search customers, invoices, quotations..." data-bs-toggle="modal" data-bs-target="#globalSearchModal" readonly>
    <span class="kbd-hint d-none d-xl-inline-block">Ctrl K</span>
  </div>

  <div class="topbar-actions">
    <div class="dropdown">
      <button class="icon-btn" data-bs-toggle="dropdown" title="Notifications" id="notifBellBtn">
        <i class="bi bi-bell-fill"></i>
        <span class="dot-badge" id="notifBadge">0</span>
      </button>
      <div class="dropdown-menu dropdown-menu-end p-0" style="width:360px;">
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
          <strong class="fs-13">Notifications</strong>
          <a href="#" class="fs-12 text-primary-KTG fw-600" id="notifMarkAllRead">Mark all read</a>
        </div>
        <div style="max-height:340px; overflow-y:auto;" class="p-2" id="notifDropdownList"></div>
        <div class="text-center border-top py-2"><a href="notifications.php" class="fs-12 fw-600 text-primary-KTG">View all notifications</a></div>
      </div>
    </div>

    <button class="icon-btn d-none d-md-flex" title="Help"><i class="bi bi-question-circle"></i></button>

    <div class="topbar-divider d-none d-md-block"></div>

    <div class="dropdown">
      <div class="topbar-user" data-bs-toggle="dropdown">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($__topbarUser['name'] ?? 'User'); ?>&background=<?php echo urlencode(ltrim($__topbarUser['avatar_color'] ?? '#2563eb', '#')); ?>&color=fff&bold=true" alt="user">
        <div class="d-none d-md-block">
          <div class="t-name"><?php echo htmlspecialchars($__topbarUser['name'] ?? '', ENT_QUOTES); ?></div>
          <div class="t-role"><?php echo htmlspecialchars($__topbarUser['role'] ?? '', ENT_QUOTES); ?></div>
        </div>
        <i class="bi bi-chevron-down fs-12 text-muted d-none d-md-block"></i>
      </div>
      <div class="dropdown-menu dropdown-menu-end">
        <span class="dropdown-header">Signed in as <span class="js-user-email"><?php echo htmlspecialchars($__topbarUser['email'] ?? '', ENT_QUOTES); ?></span></span>
        <a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i>My Profile</a>
        <a class="dropdown-item" href="settings.php"><i class="bi bi-gear"></i>Settings</a>
        <a class="dropdown-item" href="settings.php"><i class="bi bi-shield-lock"></i>Roles &amp; Permissions</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger js-logout" href="#"><i class="bi bi-box-arrow-right"></i>Logout</a>
      </div>
    </div>
  </div>
</header>

<!-- Global Search Modal -->
<div class="modal fade" id="globalSearchModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="p-3 border-bottom d-flex align-items-center gap-2">
          <i class="bi bi-search text-muted fs-5"></i>
          <input type="text" class="form-control border-0 shadow-none fs-6" placeholder="Search customers, projects, quotations, invoices, payments..." style="font-size:15px" autofocus>
          <button class="btn btn-sm btn-light" data-bs-dismiss="modal">Esc</button>
        </div>
        <div class="p-3">
          <div class="fs-11 text-muted fw-700 text-uppercase mb-2">Quick links</div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="customers.php" class="chip chip-inprogress"><i class="bi bi-people"></i>Customers</a>
            <a href="quotation-list.php" class="chip chip-inprogress"><i class="bi bi-file-text"></i>Quotations</a>
            <a href="invoice-list.php" class="chip chip-inprogress"><i class="bi bi-receipt"></i>Invoices</a>
            <a href="payments.php" class="chip chip-inprogress"><i class="bi bi-cash"></i>Payments</a>
          </div>
          <div class="fs-11 text-muted fw-700 text-uppercase mb-2">Recent</div>
          <a href="customer-details.php" class="dropdown-item px-2"><i class="bi bi-building"></i>Zen Foods Pvt Ltd</a>
          <a href="invoice-create.php" class="dropdown-item px-2"><i class="bi bi-receipt"></i>INV-2026-0150</a>
          <a href="quotation-create.php" class="dropdown-item px-2"><i class="bi bi-file-text"></i>QUO-2026-0091</a>
        </div>
      </div>
    </div>
  </div>
</div>
