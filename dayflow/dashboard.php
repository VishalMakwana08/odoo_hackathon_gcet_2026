<?php
session_start();
require_once 'db.php';
// Auth Guard: Admin access check [cite: 9, 13]
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?action=signin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dayflow Admin | Management Control</title>
    <style>
        :root { --primary: #2563EB; --bg: #F8FAFC; --card: #FFFFFF; --text: #1E293B; --border: #E2E8F0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar Styles [cite: 46] */
        .sidebar { width: 260px; background: #0F172A; color: white; display: flex; flex-direction: column; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: var(--primary); margin: 0 0 35px 0; font-size: 24px; font-weight: 800; letter-spacing: -1px; }
        .nav-link { padding: 12px 15px; border-radius: 8px; cursor: pointer; transition: 0.3s; margin-bottom: 5px; display: flex; align-items: center; color: #94A3B8; text-decoration: none; font-size: 14px; }
        .nav-link:hover, .nav-link.active { background: #1E293B; color: white; }
        .nav-link.active { border-left: 4px solid var(--primary); background: #1E293B; color: white; }
        
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-bar { background: white; padding: 18px 30px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .workspace { padding: 30px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        
        .card { background: var(--card); border-radius: 12px; border: 1px solid var(--border); padding: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .grid-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-card { padding: 20px; border-radius: 10px; border: 1px solid; }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
        th { text-align: left; font-size: 12px; color: #64748B; padding: 12px; text-transform: uppercase; border-bottom: 2px solid #F1F5F9; }
        td { padding: 14px 12px; font-size: 14px; background: #F9FAFB; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .Pending { background: #FEF3C7; color: #92400E; }
        .Approved { background: #D1FAE5; color: #065F46; }
        .Rejected { background: #FEE2E2; color: #991B1B; }

        .btn-action { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; margin-right: 5px; }
        .btn-approve { background: #10B981; color: white; }
        .btn-reject { background: #EF4444; color: white; }

        .modal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); }
        .modal-content { background: white; margin: 6% auto; padding: 30px; border-radius: 16px; width: 450px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Dayflow</h2>
        <a class="nav-link active" onclick="location.reload()">Admin Overview</a>
        <a class="nav-link" onclick="loadAdminModule('employees', this)">Employee Directory</a>
        <a class="nav-link" onclick="loadAdminModule('attendance', this)">Attendance Logs</a>
        <a class="nav-link" onclick="loadAdminModule('leaves', this)">Leave Requests</a>
        <a class="nav-link" onclick="loadAdminModule('payroll', this)">Payroll Control</a>
        <a href="auth/logout.php" class="nav-link" style="margin-top: auto; color: #F87171;">Logout</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <span style="font-weight: 600; color: #64748B;">Admin / <span id="breadCrumb">Overview</span></span>
            <div style="font-size: 14px;"><strong>HR Administrator</strong></div>
        </div>

        <div class="workspace" id="adminContainer">
            <div class="grid-stats">
                <div class="stat-card" style="background:#EFF6FF; border-color:#DBEAFE; color:#1E40AF;">
                    <small>TOTAL EMPLOYEES</small>
                    <h2 id="count-emp">...</h2>
                </div>
                <div class="stat-card" style="background:#ECFDF5; border-color:#D1FAE5; color:#065F46;">
                    <small>TODAY'S ATTENDANCE</small>
                    <h2 id="count-att">...</h2>
                </div>
                <div class="stat-card" style="background:#FFFBEB; border-color:#FEF3C7; color:#92400E;">
                    <small>PENDING LEAVES</small>
                    <h2 id="count-leave">...</h2>
                </div>
            </div>
            <div class="card">
                <h3>Welcome back, Admin</h3>
                <p>Use the sidebar to manage core HR operations and approval workflows.</p>
            </div>
        </div>
    </div>

    <div id="payrollModal" class="modal">
        <div class="modal-content">
            <h3 style="text-align:center">Update Salary Structure</h3>
            <form id="payrollForm">
                <input type="hidden" name="user_id" id="modal_uid">
                <div style="margin-bottom:15px">
                    <label style="display:block; font-size:13px; font-weight:600">Basic Salary (₹)</label>
                    <input type="number" name="basic" id="modal_basic" style="width:100%; padding:10px; border-radius:8px; border:1px solid #DDD" required>
                </div>
                <div style="margin-bottom:15px">
                    <label style="display:block; font-size:13px; font-weight:600">HRA (₹)</label>
                    <input type="number" name="hra" id="modal_hra" style="width:100%; padding:10px; border-radius:8px; border:1px solid #DDD">
                </div>
                <div style="margin-bottom:15px">
                    <label style="display:block; font-size:13px; font-weight:600">Deductions (₹)</label>
                    <input type="number" name="deductions" id="modal_deduct" style="width:100%; padding:10px; border-radius:8px; border:1px solid #DDD">
                </div>
                <button type="button" onclick="savePayroll()" style="background:var(--primary); color:white; width:100%; padding:12px; border:none; border-radius:8px; font-weight:600; cursor:pointer">Save Changes</button>
                <button type="button" onclick="closeModal()" style="background:none; border:none; width:100%; margin-top:10px; cursor:pointer; color:#64748B">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function loadAdminModule(module, element) {
        document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
        if(element) element.classList.add('active');
        document.getElementById('breadCrumb').innerText = module.toUpperCase();
        
        fetch(`api/admin_api.php?module=${module}`)
        .then(res => res.text())
        .then(data => { 
            document.getElementById('adminContainer').innerHTML = `<div class="card"><h3>${module.toUpperCase()} MANAGEMENT</h3>${data}</div>`; 
        });
    }

    function updateStats() {
        fetch('api/admin_api.php?module=dashboard_counts')
        .then(res => res.json())
        .then(data => {
            document.getElementById('count-emp').innerText = data.employees;
            document.getElementById('count-att').innerText = data.attendance;
            document.getElementById('count-leave').innerText = data.leaves;
        });
    }

    function manageLeave(id, status) {
        const f = new FormData(); f.append('leave_id', id); f.append('status', status); f.append('action', 'manage_leave');
        fetch('api/admin_api.php', { method:'POST', body:f }).then(res => res.text()).then(d => { alert(d); loadAdminModule('leaves'); });
    }

    function openPayrollModal(uid) { document.getElementById('modal_uid').value = uid; document.getElementById('payrollModal').style.display='block'; }
    function closeModal() { document.getElementById('payrollModal').style.display='none'; }
    function savePayroll() {
        const formData = new FormData(document.getElementById('payrollForm'));
        formData.append('action', 'set_salary');
        fetch('api/admin_api.php', { method: 'POST', body: formData })
        .then(res => res.text()).then(data => { alert(data); closeModal(); loadAdminModule('payroll'); });
    }

    updateStats();
    </script>
</body>
</html>