<?php
session_start();
require_once 'settings.php';

include 'header.inc';
?>

        <h1>Order Management</h1>

        <div class="manager-actions">
            <form method="get" action="manager.php" class="search-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search_name">Search by Name:</label>
                        <input type="text" id="search_name" name="search_name" 
                               value="<?php echo isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="search_product">Product:</label>
                        <select id="search_product" name="search_product">
                            <option value="">All Products</option>
                            <option value="aurora" <?php echo (isset($_GET['search_product']) && $_GET['search_product'] == 'aurora') ? 'selected' : ''; ?>>Aurora Series</option>
                            <option value="nexus" <?php echo (isset($_GET['search_product']) && $_GET['search_product'] == 'nexus') ? 'selected' : ''; ?>>Nexus Series</option>
                            <option value="essence" <?php echo (isset($_GET['search_product']) && $_GET['search_product'] == 'essence') ? 'selected' : ''; ?>>Essence Series</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="search_status">Status:</label>
                        <select id="search_status" name="search_status">
                            <option value="">All Statuses</option>
                            <option value="PENDING" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'PENDING') ? 'selected' : ''; ?>>Pending</option>
                            <option value="FULFILLED" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'FULFILLED') ? 'selected' : ''; ?>>Fulfilled</option>
                            <option value="PAID" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'PAID') ? 'selected' : ''; ?>>Paid</option>
                            <option value="ARCHIVED" <?php echo (isset($_GET['search_status']) && $_GET['search_status'] == 'ARCHIVED') ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sort_by">Sort By:</label>
                        <select id="sort_by" name="sort_by">
                            <option value="order_time" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'order_time') ? 'selected' : ''; ?>>Order Date</option>
                            <option value="order_cost" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'order_cost') ? 'selected' : ''; ?>>Total Cost</option>
                            <option value="lastname" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'lastname') ? 'selected' : ''; ?>>Customer Name</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">Order:</label>
                        <select id="sort_order" name="sort_order">
                            <option value="DESC" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'DESC') ? 'selected' : ''; ?>>Descending</option>
                            <option value="ASC" <?php echo (isset($_GET['sort_order']) && $_GET['sort_order'] == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="submit-button">Search</button>
                        <a href="manager.php" class="reset-button">Show All</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="orders-table">
            <?php
            // First, let's check if the orders table exists
            $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
            if (mysqli_num_rows($table_check) == 0) {
                echo "<p class='no-orders'>The orders table doesn't exist yet. Please run create_tables.php first.</p>";
            } else {
                // Build query based on search parameters
                $where_conditions = array();
                $params = array();
                $types = '';
                
                if (isset($_GET['search_name']) && !empty($_GET['search_name'])) {
                    $where_conditions[] = "(firstname LIKE ? OR lastname LIKE ?)";
                    $search_name = "%" . $_GET['search_name'] . "%";
                    $params[] = $search_name;
                    $params[] = $search_name;
                    $types .= 'ss';
                }
                
                if (isset($_GET['search_product']) && !empty($_GET['search_product'])) {
                    $where_conditions[] = "product = ?";
                    $params[] = $_GET['search_product'];
                    $types .= 's';
                }
                
                if (isset($_GET['search_status']) && !empty($_GET['search_status'])) {
                    $where_conditions[] = "order_status = ?";
                    $params[] = $_GET['search_status'];
                    $types .= 's';
                }
                
                $where_sql = '';
                if (!empty($where_conditions)) {
                    $where_sql = "WHERE " . implode(' AND ', $where_conditions);
                }
                
                $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'order_time';
                $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';
                
                // Validate sort columns to prevent SQL injection
                $allowed_columns = array('order_time', 'order_cost', 'lastname');
                if (!in_array($sort_by, $allowed_columns)) {
                    $sort_by = 'order_time';
                }
                $sort_order = strtoupper($sort_order) == 'ASC' ? 'ASC' : 'DESC';
                
                $sql = "SELECT * FROM orders $where_sql ORDER BY $sort_by $sort_order";
                
                if (!empty($params)) {
                    $stmt = mysqli_prepare($conn, $sql);
                    if ($stmt) {
                        // Bind parameters manually
                        $bind_params = array();
                        $bind_params[] = $types;
                        foreach ($params as $key => $value) {
                            $bind_params[] = &$params[$key];
                        }
                        
                        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($stmt), $bind_params));
                        
                        if (mysqli_stmt_execute($stmt)) {
                            $result = mysqli_stmt_get_result($stmt);
                            
                            if (mysqli_num_rows($result) > 0): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Total Cost</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></td>
                                            <td><?php echo ucfirst($order['product']); ?> Series</td>
                                            <td><?php echo $order['quantity']; ?></td>
                                            <td>$<?php echo number_format($order['order_cost'], 2); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($order['order_time'])); ?></td>
                                            <td>
                                                <form method="post" action="update_order.php" class="status-form">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="order_status" onchange="this.form.submit()">
                                                        <option value="PENDING" <?php echo $order['order_status'] == 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="FULFILLED" <?php echo $order['order_status'] == 'FULFILLED' ? 'selected' : ''; ?>>Fulfilled</option>
                                                        <option value="PAID" <?php echo $order['order_status'] == 'PAID' ? 'selected' : ''; ?>>Paid</option>
                                                        <option value="ARCHIVED" <?php echo $order['order_status'] == 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td>
                                                <?php if ($order['order_status'] == 'PENDING'): ?>
                                                <form method="post" action="cancel_order.php" class="cancel-form">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <button type="submit" class="cancel-button" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                                                </form>
                                                <?php else: ?>
                                                <span class="no-cancel">Cannot cancel</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="no-orders">No orders found matching your criteria.</p>
                            <?php endif;
                            
                            mysqli_stmt_close($stmt);
                        } else {
                         error_log("Error executing query in manager.php: " . mysqli_error($conn));
                         echo "<p class='no-orders'>Error executing query. Please try again.</p>";
}
                    } else {
                     error_log("Error preparing statement in manager.php: " . mysqli_error($conn));
                     echo "<p class='no-orders'>Error preparing statement. Please try again.</p>";
}
                    // No parameters, use simple query
                    $result = mysqli_query($conn, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total Cost</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['firstname'] . ' ' . $order['lastname']); ?></td>
                                    <td><?php echo ucfirst($order['product']); ?> Series</td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td>$<?php echo number_format($order['order_cost'], 2); ?></td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($order['order_time'])); ?></td>
                                    <td>
                                        <form method="post" action="update_order.php" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="order_status" onchange="this.form.submit()">
                                                <option value="PENDING" <?php echo $order['order_status'] == 'PENDING' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="FULFILLED" <?php echo $order['order_status'] == 'FULFILLED' ? 'selected' : ''; ?>>Fulfilled</option>
                                                <option value="PAID" <?php echo $order['order_status'] == 'PAID' ? 'selected' : ''; ?>>Paid</option>
                                                <option value="ARCHIVED" <?php echo $order['order_status'] == 'ARCHIVED' ? 'selected' : ''; ?>>Archived</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($order['order_status'] == 'PENDING'): ?>
                                        <form method="post" action="cancel_order.php" class="cancel-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <button type="submit" class="cancel-button" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel</button>
                                        </form>
                                        <?php else: ?>
                                        <span class="no-cancel">Cannot cancel</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="no-orders">No orders found in the system.</p>
                    <?php endif;
                    
                    if ($result) {
                        mysqli_free_result($result);
                    }
                }
            }
            
            mysqli_close($conn);
            ?>
        </div>

<?php include 'footer.inc'; ?>