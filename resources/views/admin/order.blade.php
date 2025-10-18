<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order - VogueVault</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #EAE9D3;
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
    }

    .sidebar {
      background-color: #ffffff;
      border-radius: 20px;
      padding: 24px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #333;
      padding: 8px;
      border-radius: 8px;
      transition: 0.2s;
    }

    .menu-item:hover {
      background-color: #f3f3f3;
    }

    .menu-item.active {
      font-weight: 600;
      background-color: #f5f5f5;
    }

    main {
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow: hidden;
    }

    .table-container {
      flex-grow: 1;
      background: white;
      border-radius: 16px;
      border: 1px solid #d1d5db;
      padding: 20px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.05);
      overflow-y: auto;
    }

    select {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 4px 8px;
      font-size: 0.875rem;
      outline: none;
      background-color: #f9fafb;
    }

    select:focus {
      border-color: #9ca3af;
      background-color: white;
    }
  </style>
</head>
<body>

  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 sidebar m-4">
      <div>
        <div class="flex items-center mb-10 space-x-2">
          <img src="https://cdn-icons-png.flaticon.com/512/891/891462.png" class="w-6 h-6" alt="">
          <h1 class="text-lg font-bold">VogueVault</h1>
        </div>

        <nav class="space-y-3">
          <a href="#" class="menu-item">
            <span>📊</span> <span>Dashboard</span>
          </a>
          <a href="#" class="menu-item">
            <span>📦</span> <span>Product</span>
          </a>
          <a href="#" class="menu-item active">
            <span>🧾</span> <span>Order</span>
          </a>
          <a href="#" class="menu-item">
            <span>🔔</span> <span>Notification</span>
          </a>
          <a href="#" class="menu-item">
            <span>❓</span> <span>Help</span>
          </a>
        </nav>
      </div>

      <div class="text-sm text-gray-500 flex items-center gap-2">
        <span>👤</span> <span>Username</span>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-semibold">Recent Purchases</h2>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="text-sm text-left w-full border-collapse">
          <thead class="border-b sticky top-0 bg-white">
            <tr class="font-semibold text-gray-700">
              <th class="p-3 w-8"><input type="checkbox"></th>
              <th class="p-3">Product</th>
              <th class="p-3">Order ID</th>
              <th class="p-3">Date</th>
              <th class="p-3">Customer Name</th>
              <th class="p-3">Status</th>
              <th class="p-3">Amount</th>
            </tr>
          </thead>
          <tbody class="text-gray-600">
            @for ($i = 0; $i < 50; $i++)
              @php
                $orderId = sprintf("#%06d", $i + 1); // format jadi #000001, #000002, dst
              @endphp
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3"><input type="checkbox"></td>
                <td class="p-3">Product {{ $i+1 }}</td>
                <td class="p-3 font-mono text-gray-700">{{ $orderId }}</td>
                <td class="p-3">2025-10-08</td>
                <td class="p-3">Customer {{ $i+1 }}</td>
                <td class="p-3">
                  <select class="status-select" onchange="updateStatusColor(this)">
                    <option value="completed" selected>Completed</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="canceled">Canceled</option>
                  </select>
                </td>
                <td class="p-3">$ {{ $i * 10 }}</td>
              </tr>
            @endfor
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Script ubah warna status -->
  <script>
    function updateStatusColor(select) {
      const colorMap = {
        completed: 'bg-green-100 text-green-700 border-green-400',
        pending: 'bg-yellow-100 text-yellow-700 border-yellow-400',
        processing: 'bg-blue-100 text-blue-700 border-blue-400',
        canceled: 'bg-red-100 text-red-700 border-red-400'
      };

      select.className = 'status-select border rounded px-2 py-1 text-sm';
      const selectedValue = select.value;
      select.classList.add(...colorMap[selectedValue].split(' '));
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.status-select').forEach(select => updateStatusColor(select));
    });
  </script>

</body>
</html>
