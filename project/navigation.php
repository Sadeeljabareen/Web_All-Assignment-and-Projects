<nav>
    <ul>
        <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="add_project.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_project.php' ? 'active' : ''; ?>">Add Project</a></li>
        <li><a href="allocate_team_leader.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'allocate_team_leader.php' ? 'active' : ''; ?>">Allocate Team Leader</a></li>
        <li><a href="create_task.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'create_task.php' ? 'active' : ''; ?>">Create Task</a></li>
        <li><a href="assign_team_members.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'assign_team_members.php' ? 'active' : ''; ?>">Assign Team Members</a></li>
        <li><a href="accept_task.php" class="<?php echo (isset($_SESSION['new_tasks']) && $_SESSION['new_tasks']) ? 'highlight' : (basename($_SERVER['PHP_SELF']) == 'accept_task.php' ? 'active' : ''); ?>">Assignments</a></li>
        <li><a href="update_progress.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'update_progress.php' ? 'active' : ''; ?>">Update Progress</a></li>
        <li><a href="task_search.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'task_search.php' ? 'active' : ''; ?>">Task Search</a></li>
        <li><a href="logout.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'logout.php' ? 'active' : ''; ?>">Logout</a></li>
    </ul>
</nav>