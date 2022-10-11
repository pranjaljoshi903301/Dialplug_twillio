<!doctype html>
<html lang="en">
  <head>    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css">

    <title>DialPlug Exotel Home</title>
    <style>
      body{        
        background: #eef2f4 !important;
      }
      .text-style{
        color: #525c69 !important;        
      }
      .heading{
        font-size: 24px;
        padding-bottom: 2%;
        display: block;
        width: 100%;
        white-space: nowrap;
      }
      .box-1{
        background: white;
        padding: 15px 15px 20px;
      }
      .box{
        background: #f8f9fa;
        padding: 15px 15px 20px;
      }
      .form-control:focus{
        border-color: #66afe9;
        box-shadow: none;
        outline: 0 none;
      }
      .validation-failed{
        outline: 1px solid red;
      }
      .form-control{
        border-radius: 0px;
      }
      .popoverbtn{
        border: none;
        outline:none;
        cursor: pointer;
      }
      .btn_cancel{
        border: none;
        outline:none;
        box-shadow: none;                
        border-radius: 2px;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        white-space: nowrap;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;        
        background: #eef2f4;
        color: #535c69;
      }
      .btn_save{
        border: none;
        outline:none;
        box-shadow: none;
        background: #bbed21;
        color: #535c69;
        border-radius: 2px;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        white-space: nowrap;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;        
      }
      .form-group{
        margin-right: 39px;
        margin-left: 10px;
      }
      .table{
        border-spacing: 0 !important;
      }
      table td{
        color: #535c69;                
        letter-spacing: .3px;
        white-space: nowrap;        
      }
      table th{
        text-transform: uppercase;
        color: #535c69;                
        white-space: nowrap;
        opacity: .8;
        letter-spacing: .5px;        
      }
      table .action:hover{
        opacity: 1;
      }      
      table .action{
        color: #535c69;                
        opacity: .5;
      }
    </style>
  </head>
  <body>    
    
    @yield('content')    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js"></script>    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>    
    <script>
      $(function () {
        $('[data-toggle="popover"]').popover();
        $('#datatable').DataTable();
      });
    </script>    

  </body>
</html>