<?php 
session_start();
$_SESSION = array(); // Clear all session variables
session_destroy(); // Destroy session

// Notify all tabs about the logout
echo "<script>
    localStorage.setItem('forceLogout', Date.now());
    window.location.href = 'index.php';
</script>";
exit;
?>
