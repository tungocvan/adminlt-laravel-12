<div x-data="tablesData">
    <div class="row">
        <div class="col-md-12">
            <div id="jsGrid"></div>
        </div>
    </div>
</div>
@script
 <script>
        Alpine.data("tablesData", () => ({
            perPage:5,  
            gridData:[],
            search:'',
            init(){                                            
            },
        }))
        document.addEventListener('livewire:initialized', () => {
            var handlerSubmit = function(value) {
                let btnEdit = `<a href='/admin/products/edit/${value}' class='mx-1'><input class='jsgrid-button jsgrid-edit-button' type='button' title='Edit'></a>`
                let btnDelete = `<a href='/admin/products/delete/${value}' class='mx-1'><input class='jsgrid-button jsgrid-delete-button' type='button' title='Delete'></a>`;
                return  btnEdit + btnDelete
            }
            var MyPriceField = function(config) {
                jsGrid.Field.call(this, config);
            };
            MyPriceField.prototype = new jsGrid.Field({
                itemTemplate: function(value) {
                    return formatCurrency(value);
                },
            })
            var MyImageField = function(config) {
                jsGrid.Field.call(this, config);
            };
            MyImageField.prototype = new jsGrid.Field({
                itemTemplate: function(value) { 
                    return `<img alt='Avatar' class='table-avatar' src='/storage/${value}' width='50' height='50'>`;
                },
            })
            var MyDateField = function(config) {
                jsGrid.Field.call(this, config);
            };
            MyDateField.prototype = new jsGrid.Field({
                itemTemplate: function(value) {
                    return formatDate(value);
                },
            })
            var MyCheckBoxField = function(config) {
                jsGrid.Field.call(this, config);
            };
            MyCheckBoxField.prototype = new jsGrid.Field({
                headerTemplate: function() {
                    return `
                        <input type='checkbox' />
                    `
                },
                itemTemplate: function(value) {
                    return `<input type='checkbox' value='${value}' />`
                },
            })
            var MyButtonField = function(config) {
                jsGrid.Field.call(this, config);
            };
            MyButtonField.prototype = new jsGrid.Field({
                        headerTemplate: function() {
                            return "Action"
                        },                       
                        itemTemplate: function(value) {
                            return handlerSubmit(value)
                        },   
                        align:'center',
                        width:10
            })
            var db = @json($posts);       

            console.log(db);
            jsGrid.fields.myPriceField = MyPriceField;
            jsGrid.fields.myImageField = MyImageField;
            jsGrid.fields.myDateField =  MyDateField;
            jsGrid.fields.myCheckBoxField =  MyCheckBoxField;
            jsGrid.fields.myButtonField =  MyButtonField;
            $("#jsGrid").jsGrid({
                width: "100%",
                height: "100%",                 
                data: db ,                
                fields: [
                    { name: "ID", type: "myCheckBoxField", width:5,  align: "center" },
                    { name: "post_title",title:"Title", type: "text", width: 150, validate: "required" },                   
                    { name: "_price",title:"Price", type: "myPriceField", width: 50, align: "right" },              
                    { name: "_regular_price",title:"Regular Price", type: "myPriceField", width: 50, align: "right" },              
                    { name: "guid",title:"Image", type: "myImageField", width: 50, height:50, align: "center" },              
                    { name: "post_date",title:"Post Date", type: "myDateField", width: 50, height:50, align: "center" },     
                    { name: "ID", type: "myButtonField", width:15,  align: "center" },                             
                ]
            })
        })
        function formatCurrency(value) {
            // Kiểm tra xem giá trị có hợp lệ không
            
            if (isNaN(value)) {
                return '';
            }            
            // Chuyển đổi giá trị thành số
            const numberValue = parseFloat(value);
            // Chuyển đổi giá trị thành số và định dạng
            return numberValue.toLocaleString('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }
        function formatDate(value) {
            // Kiểm tra xem giá trị có hợp lệ không
            if (!value) {
                return '';
            }

            // Chuyển đổi chuỗi thành đối tượng Date
            const date = new Date(value);

            // Kiểm tra xem đối tượng Date có hợp lệ không
            if (isNaN(date.getTime())) {
                return '';
            }

            // Định dạng ngày tháng năm
            const options = { year: 'numeric', month: 'numeric', day: 'numeric' };
            return date.toLocaleDateString('vi-VN', options);
        }
        function handlerEdit(value){
            console.log(value);
        }
 </script>   
@endscript