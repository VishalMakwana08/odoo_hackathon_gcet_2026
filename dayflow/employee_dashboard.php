<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: index.php?action=signin");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dayflow | Employee Dashboard</title>
    <style>
        :root { --primary: #2563EB; --bg: #F8FAFC; --card: #FFFFFF; --text: #1E293B; --border: #E2E8F0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; color: var(--text); display: flex; height: 100vh; overflow: hidden; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #0F172A; color: white; display: flex; flex-direction: column; padding: 25px; box-sizing: border-box; }
        .sidebar h2 { color: var(--primary); margin: 0 0 40px 0; font-size: 26px; font-weight: 800; letter-spacing: -1px; }
        .nav-link { padding: 14px 18px; border-radius: 10px; cursor: pointer; transition: 0.3s; margin-bottom: 8px; display: flex; align-items: center; color: #94A3B8; text-decoration: none; font-size: 15px; font-weight: 500; }
        .nav-link:hover, .nav-link.active { background: #1E293B; color: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .nav-link.active { border-left: 4px solid var(--primary); }
        
        /* Main Area */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .top-bar { background: white; padding: 18px 35px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .workspace { padding: 35px; max-width: 1200px; margin: 0 auto; width: 100%; box-sizing: border-box; }
        
        /* Cards */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; }
        .card { background: var(--card); border-radius: 16px; border: 1px solid var(--border); padding: 28px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .card h3 { margin: 0 0 20px 0; font-size: 20px; color: #111827; font-weight: 700; }

        /* Widgets */
        .att-box { text-align: center; padding: 25px; background: #F9FAFB; border-radius: 14px; border: 1px solid #F3F4F6; }
        .btn-att { width: 100%; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.2s; margin-top: 15px; font-size: 14px; }
        .in { background: #10B981; color: white; }
        .out { background: #EF4444; color: white; }

        /* Forms */
        label { font-size: 13px; font-weight: 600; color: #4B5563; display: block; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 10px; margin-bottom: 18px; box-sizing: border-box; outline: none; transition: 0.2s; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
        .submit-btn { background: var(--primary); color: white; border: none; padding: 14px; width: 100%; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 15px; }

        /* Data Display */
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; font-size: 12px; color: #9CA3AF; padding: 12px; text-transform: uppercase; }
        td { padding: 15px 12px; font-size: 14px; background: #F9FAFB; }
        td:first-child { border-radius: 10px 0 0 10px; }
        td:last-child { border-radius: 0 10px 10px 0; }
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; }
        .Pending { background: #FEF3C7; color: #92400E; }
        .Approved { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Dayflow</h2>
        <a href="javascript:void(0)" class="nav-link active" onclick="location.reload()">Dashboard</a>
        <a href="javascript:void(0)" class="nav-link" onclick="loadModule('profile')">My Profile</a>
        <a href="javascript:void(0)" class="nav-link" onclick="loadModule('attendance')">Attendance</a>
        <a href="javascript:void(0)" class="nav-link" onclick="loadModule('payroll')">My Payroll</a>
        <a href="auth/logout.php" class="nav-link" style="margin-top: auto; color: #FB7185;">Logout</a>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <span style="font-weight: 700; color: #6B7280;">Dayflow / <span id="breadCrumb">Overview</span></span>
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="text-align: right">
                    <div style="font-size: 14px; font-weight: 700; color: var(--text);">Employee</div>
                    <div style="font-size: 12px; color: #9CA3AF;">ID: <?php echo $_SESSION['emp_id']; ?></div>
                </div>
                <div style="width: 42px; height: 42px; background: var(--primary); color: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;">E</div>
            </div>
        </div>

        <div class="workspace" id="moduleContainer">
            <div class="grid">
                <div class="card">
                    <h3>Daily Attendance</h3>
                    <div class="att-box" id="att-area">
                        <p style="color: #9CA3AF;">Checking logs...</p>
                    </div>
                </div>

                <div class="card">
                    <h3>Request Leave</h3>
                    <form id="leaveForm">
                        <label>Leave Category</label>
                        <select name="leave_type" required>
                            <option value="Paid">Paid Leave</option>
                            <option value="Sick">Sick Leave</option>
                            <option value="Unpaid">Unpaid Leave</option>
                        </select>
                        <div style="display: flex; gap: 12px;">
                            <div style="flex: 1;">
                                <label>From</label>
                                <input type="date" name="start_date" required>
                            </div>
                            <div style="flex: 1;">
                                <label>To</label>
                                <input type="date" name="end_date" required>
                            </div>
                        </div>
                        <label>Reason</label>
                        <textarea name="remarks" rows="2" placeholder="Describe your reason..."></textarea>
                        <button type="submit" class="submit-btn">Send Application</button>
                    </form>
                </div>

                <div class="card" style="grid-column: 1 / -1;">
                    <h3>Recent Leave Requests</h3>
                    <div id="leave-history"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // AJAX for Leave Application
    document.getElementById('leaveForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'apply_leave');
        fetch('api/employee_api.php', { method: 'POST', body: formData })
        .then(res => res.text()).then(data => { 
            alert(data); 
            this.reset(); 
            loadLeaveHistory(); 
        });
    });

    // Attendance Widgets
    function loadAttendance() {
        fetch('api/employee_api.php?action=get_att')
        .then(res => res.text()).then(data => { document.getElementById('att-area').innerHTML = data; });
    }

    function markAttendance(type) {
        const formData = new FormData();
        formData.append('action', type);
        fetch('api/employee_api.php', { method: 'POST', body: formData })
        .then(res => res.text()).then(data => { alert(data); loadAttendance(); });
    }

    function loadLeaveHistory() {
        fetch('api/employee_api.php?action=get_leaves')
        .then(res => res.text()).then(data => { document.getElementById('leave-history').innerHTML = data; });
    }

    // Module Loader (Profile, Attendance History, Payroll)
    function loadModule(module) {
        const mainView = document.getElementById('moduleContainer');
        const breadCrumb = document.getElementById('breadCrumb');
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
            if(link.innerText.toLowerCase().includes(module)) link.classList.add('active');
        });

        if (module === 'profile') {
            breadCrumb.innerText = "My Profile";
            fetch('api/employee_api.php?action=get_profile')
                .then(res => res.json())
                .then(data => {
                    mainView.innerHTML = `
                        <div class="card" style="max-width: 600px; margin: 0 auto;">
                            <h3>Employee Profile</h3>
                            <div style="background: #F9FAFB; padding: 20px; border-radius: 12px; border: 1px solid #F3F4F6;">
                                <p><strong>ID:</strong> ${data.employee_id}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Role:</strong> <span class="badge" style="background:#DBEAFE; color:#1E40AF">${data.role}</span></p>
                                <p><strong>Member Since:</strong> ${data.created_at}</p>
                            </div>
                            <p style="margin-top: 20px; color: #9CA3AF; font-size: 13px;">For any corrections, please contact the HR Department.</p>
                        </div>`;
                });
        } else if (module === 'attendance') {
            breadCrumb.innerText = "Attendance History";
            fetch('api/employee_api.php?action=get_attendance_history')
                .then(res => res.text())
                .then(data => {
                    mainView.innerHTML = `<div class="card"><h3>Complete Attendance Logs</h3>${data}</div>`;
                });
        } else if (module === 'payroll') {
            breadCrumb.innerText = "Salary Visibility";
            fetch('api/employee_api.php?action=get_payroll')
            .then(res => res.json())
            .then(data => {
                if(data.message) {
                    mainView.innerHTML = `<div class="card"><h3>Payroll Details</h3><p style="color:#9CA3AF">${data.message}</p></div>`;
                    return;
                }
                mainView.innerHTML = `
                    <div class="card" style="max-width: 500px; margin: 0 auto;">
                        <h3>Salary Breakdown</h3>
                        <div style="border: 1.5px solid #F3F4F6; border-radius: 14px; overflow: hidden">
                            <table style="width:100%; border-spacing: 0;">
                                <tr style="background:#F9FAFB"><td>Basic Salary</td><td style="text-align:right">₹${data.basic_salary}</td></tr>
                                <tr><td>HRA</td><td style="text-align:right">₹${data.hra}</td></tr>
                                <tr style="background:#F9FAFB"><td>Allowances</td><td style="text-align:right">₹${data.allowances}</td></tr>
                                <tr><td style="color:#EF4444">Deductions</td><td style="text-align:right; color:#EF4444">- ₹${data.deductions}</td></tr>
                                <tr style="background:#EFF6FF"><td style="font-weight:800; color:var(--primary)">Net Salary</td><td style="text-align:right; font-weight:800; color:var(--primary)">₹${data.net_salary}</td></tr>
                            </table>
                        </div>
                    </div>`;
            });
        }
    }

    loadAttendance();
    loadLeaveHistory();
    </script>
</body>
</html>