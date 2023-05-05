<?php
   include('adminsession.php');
    $dashboard_active = 'active';
    $page_title = 'Product';
    $btn_name = 'Submit';
    $btn_color = 'primary';
   
   include('inc/head.php');
   ?>
<body>
   <!-- ======= Header ======= -->
   <?php 
      include('inc/header.php');
       ?>
   <!-- ======= Sidebar ======= -->
   <?php 
      include('inc/menu.php');
       ?>
   <main id="main" class="main">
      <div class="pagetitle">
         <h1>Product</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Product</li>
            </ol>
         </nav>
      </div>
      <!-- End Page Title -->
      <section class="section dashboard">
         <div class="row">
         <!-- Left side columns -->
         <div class="col-lg-12">
         <div class="row">
            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">
               <div class="card">
                  <div class="card-body">
                     <form action="" method='POST' enctype="multipart/form-data">
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Product Name</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Product Desc</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Selling Price</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Billing Price</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Quantity</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Images</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <div class="row md-3">
                           <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                           <h4 class="card-title ml-2">Brand</h4>
                           <div class="col-sm-12">
                              <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
                           </div>
                        </div>
                        <br>
                        <button type="submit" name="submit" class="btn btn-<?php echo $btn_color; ?>"><?php echo $btn_name; ?></button>
                     </form>
                  </div>
               </div>
            </div>
            <!-- End Customers Card -->
            <!-- End Left side columns -->
            <!-- Right side columns -->
         </div>
      </section>
      <section class="section dashboard">
         <div class="row">
         <!-- Left side columns -->
         <div class="col-lg-12">
         <div class="row">
         <!-- Customers Card -->
         <div class="col-xxl-4 col-xl-12">
            <table class =" table datatable">
               <thead>
                  <tr>
                     <th>
                      Product Name
                     </th>
                     <th>
                      
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                     <th>
                     </th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                  </tr>
               </tbody>
            </table>
            <!-- End Left side columns -->
            <!-- Right side columns -->
         </div>
      </section>
   </main>
   <!-- End #main -->
   <!-- ======= Footer ======= -->
   <?php
      include('inc/footer.php');
       ?>