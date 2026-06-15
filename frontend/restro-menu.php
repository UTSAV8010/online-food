<?php include('config/constants.php'); include('config/blocked-check.php');  ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Pasar-kita.com</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link rel="icon" 
      type="image/png" 
      href="images/logo2.png">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <style>
        .catalog-page {
            background: radial-gradient(circle at top, #f9fbff 0%, #f3f6fb 50%, #edf1f7 100%);
            padding: 36px 0 64px;
        }

        .catalog-shell {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .catalog-head {
            text-align: center;
            margin-bottom: 24px;
        }

        .catalog-eyebrow {
            position: relative;
            display: inline-block;
            font-family: "Pacifico", cursive;
            color: #0d6efd;
            font-size: 1.2rem;
            margin-bottom: 10px;
            padding: 0 62px;
            line-height: 1.1;
        }

        .catalog-eyebrow::before,
        .catalog-eyebrow::after {
            content: "";
            position: absolute;
            top: 55%;
            width: 42px;
            height: 2px;
            background: #fea116;
        }

        .catalog-eyebrow::before {
            left: 8px;
        }

        .catalog-eyebrow::after {
            right: 8px;
        }

        .catalog-title {
            margin: 0;
            color: #0f224a;
            font-weight: 800;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
        }

        .catalog-grid {
            row-gap: 22px;
        }

        .catalog-card {
            border: 0;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .catalog-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.1);
        }

        .catalog-card .img-wrapper {
            position: relative;
            width: 100%;
        }

        .catalog-card .card-img-top {
            height: 220px;
            width: 100%;
            object-fit: cover;
            border-radius: 20px 20px 0 0;
        }

        .catalog-card .cart-icon {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            color: #111;
            z-index: 2;
        }

        .catalog-card .card-body {
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .catalog-card .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            gap: 12px;
        }

        .catalog-card .card-title {
            margin: 0;
            text-align: left;
            color: #111;
            font-weight: 800;
            font-size: 1.25rem;
            line-height: 1.3;
        }

        .catalog-card .card-price {
            margin: 0;
            color: #111;
            font-weight: 800;
            font-size: 1.15rem;
            white-space: nowrap;
        }

        .catalog-card .card-desc {
            color: #555;
            font-size: 0.9rem;
            line-height: 1.45;
            margin-bottom: 24px;
            text-align: left;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .catalog-card form {
            margin-top: auto;
        }

        .catalog-btn {
            border: 0;
            border-radius: 8px;
            background-color: #e69500;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            padding: 12px 24px;
            text-transform: uppercase;
            transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .catalog-btn:hover {
            transform: translateY(-2px);
            background-color: #d18700;
            box-shadow: 0 8px 15px rgba(230, 149, 0, 0.3);
        }

        .catalog-btn:active {
            transform: translateY(2px);
            box-shadow: 0 0 0 transparent !important;
        }

        .catalog-empty {
            text-align: center;
            color: #b03a2e;
            font-weight: 700;
        }

        @media (max-width: 575.98px) {
            .catalog-shell {
                padding: 0 12px;
            }

            .catalog-card .card-img-top {
                height: 190px;
            }

            .catalog-btn {
                width: 100%;
            }
        }

.container-xxl{
        max-width:100%;
    }
.scroll-top-button:hover {
    background: #e69500;
}
.back-to-top{
    right:0px!important;
    bottom:27px !important;
}
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
</div>
        <!-- Spinner End -->

        <div class="container-xxl position-relative p-0">
            <?php include('site-hader.php'); ?>
        </div>
        <!-- Menu Start -->
        <main class="catalog-page">
        <div class="catalog-shell">
    <div class="catalog-head">
        <span class="catalog-eyebrow">Restaurant</span>
        <h2 class="catalog-title"><?php echo isset($restro_name) ? htmlspecialchars($restro_name) . ' Menu' : 'Menu'; ?></h2>
    </div>
    <div class="row catalog-grid">
        <?php
        if(isset($_GET['restro_name'])) {
            $restro_name = mysqli_real_escape_string($conn, $_GET['restro_name']); // Secure Input

            $sql = "SELECT * FROM tbl_restro_food_item WHERE restro_name='$restro_name' AND active='Yes' AND status='approved' and stock !=0";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count > 0) {
                // Display Restaurant Name as Title
                while($row = mysqli_fetch_assoc($res)) {
                    // Get the Values
                    $id = $row['id'];
                    $title = $row['title'];
                    $price = $row['price'];
                    $description = $row['description'];
                    $restro_name = $row['restro_name'];
                    $image_name = $row['image_name'];
                    ?>
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                        <div class="catalog-card">
                            <div class="img-wrapper">
                                <img src="<?php echo SITEURL; ?>restro/uploads/food/<?php echo $image_name; ?>" class="card-img-top" alt="...">
                                <div class="cart-icon"><i class="fa fa-shopping-basket"></i></div>
                            </div>
                            <div class="card-body">
                                <div class="card-header-flex">
                                    <h5 class="card-title"><?php echo $title; ?></h5>
                                    <p class="card-price">₹ <?php echo $price; ?></p>
                                </div>
                                <p class="card-desc"><?php echo $description; ?></p>
                                <form action="<?php echo SITEURL; ?>manage-cart" method="POST">
                                    <button type="submit" name="Add_To_Cart" class="catalog-btn">Order Now <i class="fa fa-utensils"></i></button>
                                    <input type="hidden" name="Item_Name" value="<?php echo $title; ?>">
                                    <input type="hidden" name="Restro_Name" value="<?php echo $restro_name; ?>">
                                    <input type="hidden" name="Price" value="<?php echo $price; ?>">
                                    <input type="hidden" name="Id" value="<?php echo $id; ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='catalog-empty'>No menu items found for <b>$restro_name</b>.</p>";
            }
        } else {
            echo "<p class='catalog-empty'>Invalid restaurant selection.</p>";
        }
        ?>
    </div>
    </div>
    </main>

        <?php include('chatbot.php'); ?>

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries --> 


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
<?php include('site-footer.php'); ?>
</body>

</html>


