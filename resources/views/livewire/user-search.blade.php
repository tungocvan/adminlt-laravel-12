<!DOCTYPE html>
<html>
<head>
    <title>Laravel 12 Select2 JS Autocomplete Search Example - ItSolutionStuff.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>
<body>
       
<div class="container">
    <div class="card mt-5">
        <h3 class="card-header p-3">Laravel 12 JQuery UI Autocomplete Search Example - ItSolutionStuff.com</h3>
        <div class="card-body">
            
            <form action="#" method="POST" enctype="multipart/form-data" class="mt-2">
                @csrf
        
                <input class="form-control" id="search" type="text">
         
                <div class="mb-3 mt-3">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
             
            </form>
        </div>
    </div>  
      
</div>
       
<script type="text/javascript">
    var path = "{{ route('autocomplete') }}";
    
    $( "#search" ).autocomplete({
        source: function( request, response ) {
          $.ajax({
            url: path,
            type: 'GET',
            dataType: "json",
            data: {
               search: request.term
            },
            success: function( data ) {
               response( data );
            }
          });
        },
        select: function (event, ui) {
           $('#search').val(ui.item.label);
           console.log(ui.item); 
           return false;
        }
      });
    
</script>
      
</body>
</html>