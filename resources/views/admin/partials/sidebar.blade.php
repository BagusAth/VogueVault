<div class="sidebar-container">
    <aside class="sidebar">
        <div class="brand">
            <!-- <div class="brand-icon">
                <i class="bi bi-shop"></i>
            </div> -->
            <img src="{{ asset('images/logo1.png') }}" alt="VogueVault logo" class="logo-image">
            VogueVault
        </div>

        <nav class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="nav-item {{ ($active ?? '') === 'products' ? 'active' : '' }}">
                <i class="bi bi-bag"></i>
                Products
            </a>
            <a href="#" class="nav-item disabled" aria-disabled="true">
                <i class="bi bi-layout-text-window"></i>
                Orders
            </a>
            <a href="#" class="nav-item disabled" aria-disabled="true">
                <i class="bi bi-bell"></i>
                Notifications
            </a>
            <a href="#" class="nav-item disabled" aria-disabled="true">
                <i class="bi bi-question-circle"></i>
                Help
            </a>
        </nav>

        <div class="nav-footer">
            <i class="bi bi-person-circle"></i>
            <div>
                <div style="font-weight:600;">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div style="font-size:12px;color:var(--muted);">Administrator</div>
            </div>
        </div>
    </aside>
</div>
