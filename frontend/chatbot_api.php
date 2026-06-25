<?php
header('Content-Type: application/json');

// Include configuration and database connection
require_once('config/constants.php');

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Get POST request action
$action = isset($_POST['action']) ? trim($_POST['action']) : '';

if (empty($action)) {
    // Check if action is sent via GET as fallback
    $action = isset($_GET['action']) ? trim($_GET['action']) : '';
}

switch ($action) {
    case 'get_categories':
        $sql = "SELECT id, title, image_name FROM tbl_category WHERE active = 'Yes' LIMIT 12";
        $res = mysqli_query($conn, $sql);
        $categories = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $categories[] = [
                    "id" => $row['id'],
                    "title" => $row['title'],
                    "image" => $row['image_name'] ? "images/category/" . $row['image_name'] : null
                ];
            }
            echo json_encode(["success" => true, "categories" => $categories]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to fetch categories"]);
        }
        break;

    case 'get_foods':
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $search = isset($_POST['query']) ? mysqli_real_escape_string($conn, trim($_POST['query'])) : '';

        $sql = "SELECT id, title, description, price, image_name, stock, restro_name FROM tbl_food WHERE active = 'Yes'";
        
        if ($category_id > 0) {
            $sql .= " AND category_id = $category_id";
        }
        if (!empty($search)) {
            $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%' OR restro_name LIKE '%$search%')";
        }
        
        $sql .= " LIMIT 6";
        
        $res = mysqli_query($conn, $sql);
        $foods = [];
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $foods[] = [
                    "id" => $row['id'],
                    "title" => $row['title'],
                    "description" => $row['description'],
                    "price" => floatval($row['price']),
                    "image" => $row['image_name'] ? "images/food/" . $row['image_name'] : null,
                    "stock" => intval($row['stock']),
                    "restaurant" => $row['restro_name']
                ];
            }
            echo json_encode(["success" => true, "foods" => $foods]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to fetch food items"]);
        }
        break;

    case 'get_coupons':
        // Fetch active standard coupons
        $coupons = [];
        $sql1 = "SELECT coupon_code, name, discount FROM tbl_coupon WHERE status = 'active' LIMIT 5";
        $res1 = mysqli_query($conn, $sql1);
        if ($res1) {
            while ($row = mysqli_fetch_assoc($res1)) {
                $coupons[] = [
                    "code" => $row['coupon_code'],
                    "name" => $row['name'],
                    "discount" => floatval($row['discount']),
                    "type" => "General"
                ];
            }
        }

        // Fetch active festival coupons
        $sql2 = "SELECT coupon_code, festival_name, discount FROM tbl_fest_coupon WHERE status = 'active' AND expire = 'active' LIMIT 5";
        $res2 = mysqli_query($conn, $sql2);
        if ($res2) {
            while ($row = mysqli_fetch_assoc($res2)) {
                $coupons[] = [
                    "code" => $row['coupon_code'],
                    "name" => $row['festival_name'] . " Offer",
                    "discount" => floatval($row['discount']),
                    "type" => "Festival"
                ];
            }
        }

        echo json_encode(["success" => true, "coupons" => $coupons]);
        break;

    case 'track_order':
        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        if ($order_id <= 0) {
            echo json_encode(["success" => false, "message" => "Invalid order ID"]);
            exit;
        }

        // Fetch order basic details
        $sql = "SELECT order_id, username, cus_name, cus_add1, cus_city, cus_phone, payment_status, order_date, total_amount, order_status, delivery_boy_name 
                FROM order_manager WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $stmt->close();

            if ($order) {
                // Fetch ordered items
                $items = [];
                $item_sql = "SELECT Item_Name, Price, Quantity, total_amount FROM online_orders_new WHERE order_id = ?";
                $item_stmt = $conn->prepare($item_sql);
                if ($item_stmt) {
                    $item_stmt->bind_param("i", $order_id);
                    $item_stmt->execute();
                    $item_result = $item_stmt->get_result();
                    while ($item_row = $item_result->fetch_assoc()) {
                        $items[] = [
                            "name" => $item_row['Item_Name'],
                            "price" => floatval($item_row['Price']),
                            "quantity" => intval($item_row['Quantity']),
                            "total" => floatval($item_row['total_amount'])
                        ];
                    }
                    $item_stmt->close();
                }

                echo json_encode([
                    "success" => true,
                    "order" => [
                        "order_id" => $order['order_id'],
                        "customer_name" => $order['cus_name'],
                        "address" => $order['cus_add1'] . ", " . $order['cus_city'],
                        "phone" => $order['cus_phone'],
                        "date" => $order['order_date'],
                        "total_amount" => floatval($order['total_amount']),
                        "order_status" => $order['order_status'],
                        "payment_status" => $order['payment_status'],
                        "delivery_boy" => $order['delivery_boy_name'] ? $order['delivery_boy_name'] : "Not Assigned",
                        "items" => $items
                    ]
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Order not found"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Database statement preparation failed"]);
        }
        break;

    case 'submit_inquiry':
        $name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
        $phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
        $subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
        $message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

        if (empty($name) || empty($phone) || empty($subject) || empty($message)) {
            echo json_encode(["success" => false, "message" => "All fields (Name, Phone, Subject, Message) are required"]);
            exit;
        }

        // Clean phone number (numeric check)
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean_phone) || strlen($clean_phone) < 10) {
            echo json_encode(["success" => false, "message" => "Please enter a valid phone number"]);
            exit;
        }

        $date = date('Y-m-d H:i:s');
        $status = 'unread';

        $sql = "INSERT INTO message (name, phone, subject, message, message_status, date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssss", $name, $clean_phone, $subject, $message, $status, $date);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Your message has been sent successfully. We will call you soon!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to save message. Please try again."]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Database query failed"]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Invalid or unknown action"]);
        break;
}

$conn->close();
?>
