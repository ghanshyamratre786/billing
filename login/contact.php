<?php
   include('adminsession.php');
$page_title = 'Contact';
   include('inc/head.php');
$btn_name = 'Submit';
$btn_color = 'primary';


if($_GET['action']==3){
$msg = "Data Has been Deleted Successfully";
}


if($_GET['dcon_id']!=''){
    mysqli_query($conn,"DELETE  FROM contact WHERE con_id='$_GET[dcon_id]'");
    $action = 3;
    echo "<script>location='contact?action=$action';</script>";
  }

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
         <h1>Contact</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Contact</li>
            </ol>
         </nav>
      </div>
      <center> <span style="color:red;"><?php echo $msg; ?></span></center>
      <br>
      <!-- End Page Title -->
      <section class="section dashboard">
         
        <div class="row">
            <div class="col-lg-12">
               <div class="card">
                  <div class="card-body">
                     <h5 class="class card-title">
                        Show Contact
                     </h5>
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">
                                S.No.
                                </th>
                                <th scope="col">
                                    Name
                                </th>
                               
                                <th scope="col">
                                    Email
                                </th>
                                <th scope="col">
                                    Subject
                                </th>
                                <th scope="col">
                                    Message
                                </th>
                                <th scope="col">
                                    Date
                                </th>
                                <th scope="col">
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn=1;
                                $sql = mysqli_query($conn,"SELECT * FROM contact order by con_id desc");
                                while($row = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><?php echo $sn++;?></td>
                                    <td>
                                        <?php echo $row['name']; ?>
                                    </td>
                                   
                                    <td>
                                    <?php echo $row['email']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['subject']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['message']; ?>
                                    </td>
                                    <td>
                                    <?php echo date('d-m-Y',strtotime($row['createdate'])); ?>
                                    </td>
                                    <td>
                                    <a href="contact?dcon_id=<?php echo $row['con_id']; ?>"  class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                    </table>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </main>
   <!-- End #main -->
   <!-- ======= Footer ======= -->
   <?php
      include('inc/footer.php');
       ?>