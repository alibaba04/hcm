<?php
//=======  : Alibaba
//Memastikan file ini tidak diakses secara langsung (direct access is not allowed)
defined( 'validSession' ) or die( 'Restricted access' ); 
?>
<script src="http://code.jquery.com/jquery-2.2.1.min.js"></script>
        <style type="text/css">
            .preloader {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              z-index: 9999;
              background-color: #fff;
          }
          .preloader .loading {
              position: absolute;
              left: 50%;
              top: 50%;
              transform: translate(-50%,-50%);
              font: 14px arial;
          }
      </style>
      <script>
        $(document).ready(function(){
            $(".preloader").fadeOut(1000);
        })
    </script>

<section class="content-header">
     
    <h1>
        Dashboard
        <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>
<section class="content">
    <div class="row">
    </div>
    <div class="row">
        <section class="col-lg-6 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <i class="ion ion-clipboard"></i>
                    <h3 class="box-title">SDM </h3>
                </div>
                <div class="box-body">
                    <ul class="todo-list">
                        <img src="./dist/img/logo-qoobah.png" width="100%" height="75%">
                    </ul>
                </div>
                <div class="box-footer clearfix no-border">
                </div>
            </div>
        </section>
    </div>
</section>