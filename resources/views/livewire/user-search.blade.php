<div>
    @section('css')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
    @stop
    @section('content')
    <div class="container">
        <div class="card mt-5">
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
    @stop
    @section('js')
        {{-- https://www.daterangepicker.com/#examples  --}}
        <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script type="text/javascript">
            var path = "{{ route('autocomplete') }}";        
            $("#search").autocomplete({
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
                    // hiển thị ô input box 
                    $('#search').val(ui.item.value)      
                    $('#search').attr('data-id', ui.item.id);       
                console.log(ui.item); 
                return false;
                }
            });
            // Custom render to allow HTML
                $.ui.autocomplete.prototype._renderItem = function(ul, item) {
                    return $("<li></li>")
                        .data("ui-autocomplete-item", item)
                        .append("<div>" + item.label + "</div>") // render HTML
                        .appendTo(ul);
                };
        </script>
    @stop
  
</div>