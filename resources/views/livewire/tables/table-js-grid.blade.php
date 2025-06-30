<div x-data="tablesData">

    <div class="row">  
        <div class="col-sm-12 col-md-12 d-flex">               
            <div class="form-group mr-2" style="width:150px">                    
                <select x-model="perPage" class="perPage form-control custom-select" >                      
                  <option value="10">Show 10 rows</option>
                  <option value="15">Show 15 rows</option>
                  <option value="20">Show 20 rows</option>
                  <option value="50">Show 50 rows</option>                      
                </select>
            </div>
            <div>
                <button  class="btn buttons-print btn-default" title="Print"><span><i class="fas fa-fw fa-lg fa-print"></i></span></button>         
                <button  x-on:click="exportToExcel"  class="btn buttons-excel buttons-html5 btn-default" title="Export to Excel" ><span><i class="fas fa-fw fa-lg fa-file-excel text-success"></i></span></button> 
                <button @click="exportDoc" class="btn  buttons-doc btn-default" title="Export File Doc" ><span><i class="fas fa-fw fa-lg fa-file-word text-primary"></i></span></button> 
                <button  @click="exportPdf" class="btn buttons-pdf buttons-html5 btn-default" title="Export to PDF"><span><i class="fas fa-fw fa-lg fa-file-pdf text-danger"></i></span></button> 
                <button @click="$store.modal.toggle()" class="btn  buttons-html5 btn-default" title="Add" ><span><i class="fas fa-fw fa-lg fa-plus-square text-primary"></i></span></button> 
                <button @click="importExcel" class="btn  buttons-html5 btn-default" title="Import File Excel" ><span><i class="fas fa-fw fa-lg fa-file-import text-success"></i></span></button>                 
                <input type="file" id="impFile" hidden>
            </div>    
            <div class="input-group input-group-md float-right mx-2" style="width:300px">
                <input type="text" x-model="search" @input.debounce.500ms="fetchSearch" class="form-control" placeholder="Search" fdprocessedid="xdmrgc">
                <div class="input-group-append" style="height:38px">
                  <button type="submit" class="btn btn-default btn-md">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>           
        </div>    
      {{-- <div class="col-md-9 mb-2"> <i class="fas fa-paperclip"></i>
            <buttonx-data @click="$store.modal.toggle()" class="btn btn-primary">Add</button>
            
      </div> --}}
      <div class="col-md-12"><div id="jsGrid"></div></div>
    </div>
    <div class="modal fade show" x-data :class="$store.modal.show && 'd-block'" id="myModal">

        <!-- Overlay backdrop -->
        <div class="modal-backdrop fade show"></div>
      
        <!-- Modal chính -->
        <div class="modal-dialog modal-dialog-centered" style="z-index:1050">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Default Modal</h4>
              <button @click="$store.modal.show=false" type="button" class="close">
                <span>&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <p>One fine body…</p>
            </div>
            <div class="modal-footer">
              <button @click="$store.modal.show=false"  type="button" class="btn btn-default">Close</button>
              <button @click="$store.modal.show=false" type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
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
        fetchSearch(){
            //alert(this.search)
            var $grid = $("#jsGrid");
           // $grid.jsGrid("option", "search", this.search);
            $grid.jsGrid("loadData",this.search);
        },
        readData(){
            var selectedIds = []; // Mảng lưu các id đã chọn
            // Duyệt qua tất cả checkbox mà được chọn
            $(".row-checkbox:checked").each(function() {
                var id = $(this).data('id'); // Lấy data-id
                if (id) {
                    selectedIds.push(id); // Lưu id vào mảng
                }
            });       
            // Nếu không có id nào được chọn thì không xuất
            if (selectedIds.length === 0) {
                alert("No items selected.");
                return;
            }
             // Lấy dữ liệu tương ứng với các ID đã chọn
            let gridData = $("#jsGrid").jsGrid("option", "data");
            return gridData.filter(item => selectedIds.includes(item.id));
           

        },
        exportToExcel(){
          
            
            // Lấy dữ liệu tương ứng với các ID đã chọn
            // var gridData = $("#jsGrid").jsGrid("option", "data");
            // var selectedData = gridData.filter(item => selectedIds.includes(item.id));
            let selectedData = this.readData()
            // Chuyển đổi dữ liệu thành định dạng phù hợp với SheetJS
            let worksheet = XLSX.utils.json_to_sheet(selectedData);
            let workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");

            // Xuất file Excel
            XLSX.writeFile(workbook, 'jsGridData.xlsx');

        },
        importExcel(){
            const inputFile = document.getElementById('impFile');
            inputFile.click();
            inputFile.onchange = (event) => {
                const file = event.target.files[0];
                if (!file) {
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });

                    // Assuming the data is in the first sheet
                    const firstSheetName = workbook.SheetNames[0];
                    const worksheet = workbook.Sheets[firstSheetName];
                    const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
                    
                    // Now you have the data in jsonData, call your Livewire method
                    //this.$wire.call('importExcel', jsonData);
                    console.log(jsonData);
                };
                reader.readAsArrayBuffer(file);
                inputFile.value = ''; // Reset the input
            }
        },
        exportPdf() {
            
           
            let selectedData = this.readData()
            // Truy cập vào jsPDF từ window object
            const { jsPDF } = window.jspdf;

            // Tạo một instance của jsPDF
            const doc = new jsPDF();
            console.log(selectedData);
            // Thêm nội dung vào tệp PDF (bạn có thể tùy chỉnh nội dung)
            doc.text("Hello, this is your PDF document!", 10, 10);
            doc.text("You can add more content here...", 10, 20);
            
            // Xuất tệp PDF
            doc.save("generated.pdf");
        },
        
        async exportDoc() {
                    // Truy cập các thành phần từ thư viện docx
                    const { Document, Packer, Paragraph, TextRun } = docx;

                    // Tạo một document mới
                    const doc = new Document({
                        sections: [{ // Định nghĩa các sections cho tài liệu
                            properties: {},
                            children: [
                                new Paragraph({
                                    children: [
                                        new TextRun("Hello, this is your DOC document!"),
                                        new TextRun({
                                            text: " This is a new line.",
                                            break: 1, // Thêm một dòng mới
                                        }),
                                        new TextRun({
                                            text: "You can add more content here...",
                                            bold: true, // In đậm
                                        }),
                                    ],
                                }),
                            ],
                        }],
                    });

                    try {
                        // Xuất tệp DOC
                        const blob = await Packer.toBlob(doc);
                        this.downloadFile(blob, "generated.docx"); // Tải xuống tệp
                    } catch (error) {
                        console.error("Error while creating the document:", error);
                    }
        },

        downloadFile(blob, fileName) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url); // Giải phóng URL
        }

    }))  
    
    Alpine.store('modal', {
        show: false,    
        toggle() {
            let content = `<h5>Add new</h5>`
            $('#myModal .modal-body').html(content)
            this.show = ! this.show            
        }
    })

    document.addEventListener('livewire:initialized', () => {        
                var selectedItems = [];
                var filter = false; 
                var idCountry = 1;
                var perPage = 5;
                var selectItem = function(item) {
                
                    selectedItems.push(item);
                    //console.log('selectItem:',selectedItems);
                    // var $grid = $("#jsGrid");
                    // $grid.jsGrid("option", "excelData", selectedItems);
                };

            

                var unselectItem = function(item) {
                    selectedItems = $.grep(selectedItems, function(i) {
                        return i !== item;
                    });
                    //console.log('unselectItem:',selectedItems);
                };

                var deleteSelectedItems = function() {
                    // console.log(selectedItems);
                    if(!selectedItems.length || !confirm("Are you sure?"))
                        return;

                    deleteClientsFromDb(selectedItems);

                    var $grid = $("#jsGrid");
                    $grid.jsGrid("option", "pageIndex", 1);
                    $grid.jsGrid("loadData");

                    selectedItems = [];
                };

                var deleteClientsFromDb = function(deletingClients) {          
                    console.log('gọi lên server xóa:',deletingClients);
                };

                function openEditModal(item) {
                    // Điền dữ liệu vào modal
                    //hanlderShow()
                //console.log(this.show);
                }
            

                $('.perPage').change(function(){                 
                    perPage = this._x_model.get('perPage')
                    //console.log(perPage);
                    var $grid = $("#jsGrid");
                    $grid.jsGrid("option", "pageSize", perPage);
                    $grid.jsGrid("loadData");
                });
            
                $('#jsGrid').on('change', '#selectAll', function() {
                    //console.log('selectAll');
                    var checked = $(this).is(':checked');
                    $(".row-checkbox").prop('checked', checked);            
                
                    // Duyệt qua tất cả các checkbox với class "row-checkbox"
                    
                    if(checked){
                        $(".row-checkbox").each(function() {
                        // Lấy id từ data-id
                            let id = $(this).data('id'); // Sử dụng data-id để lấy ID
                            if (id) {
                                selectItem(id); // Thêm id vào mảng
                            }
                        });
                    }else{
                        $(".row-checkbox").each(function() {
                        // Lấy id từ data-id
                            let id = $(this).data('id'); // Sử dụng data-id để lấy ID
                            if (id) {
                                unselectItem(id); // Thêm id vào mảng
                            }
                        });
                    }

                    
                });

                $("#jsGrid").jsGrid({
                    width: "100%",
                    height: "100%",
                    readOnly: false,
                    filtering: true,               
                    inserting: true,
                    editing: true,
                    sorting: true,
                    confirmDeleting: true,
                    paging: true,
                    autoload: true,        
                    pageSize: 5,
                    pagerFormat: "Pages: {first} {prev} {pages} {next} {last} Showing  {pageIndex} to {pageCount} of {itemCount}",
                    pageButtonCount: 5,
                    excelData:[],
                    search:'',
                
                    deleteConfirm: function(item) {
                        return "The client \"" + item.name + "\" will be removed. Are you sure?";
                    },

                    rowDoubleClick: function(args) {
                        // showDetailsDialog("Edit", args.item);
                        console.log('cập nhật rowDoubleClick:',args.item);
                        $("#jsGrid").jsGrid("editItem", args.item);
                        
                    },
                    rowClick: function(args) {
                        // showDetailsDialog("Edit", args.item);
                    // console.log('cập nhật data:',this.data);
                        console.log('cập nhật rowClick:',args.item);
                    // $("#jsGrid").jsGrid("editItem", args.item);
                        
                    },
                    onDataLoading: function(args) {                  
                    // console.log('onDataLoading');            
                    
                    // this.pageSize = perPage
                    // console.log('search:',this.search);
                    
                    },
            
                    onItemInserted: function(args) {
                    // cancel insertion of the item with empty 'name' field
                        console.log('onItemInserted:',args.item);
                        if(args.item.name === "") {
                            args.cancel = true;
                            alert("Specify the name of the item!");
                        }
                    },
                    onItemUpdated: function(args) {
                    // cancel insertion of the item with empty 'name' field
                        console.log('onItemUpdated:');
                        if(args.item.name === "") {
                            args.cancel = true;
                            alert("Specify the name of the item!");
                        }
                    },

                    onItemDeleted: function(args) {      
                        console.log('onItemDeleted:',args.item);           
                    
                    },
                    

                    // Thêm sự kiện khi hàng được chỉnh sửa
                    // onItemEditing: function(args) {
                    //     // Ngăn hành động chỉnh sửa mặc định
                    //     args.cancel = true; // Ngăn không cho jsGrid mở form chỉnh sửa tự động

                    //     console.log('open modal here');
                    //     //hanlderShow()
                
                    //     // Mở modal với dữ liệu của item được nhấn
                    //    //openEditModal(args.item);
                    //    let {name,age} = args.item                                      
                    //    let content = `<h5>${name} - ${age}</h5>`
                    //    $('#myModal .modal-body').html(content)
                    //    $store.modal.show = true
                    // },
                    
                    onItemAddModal: function(args) {
                        $store.modal.show = true
                    },
                    showDetail: function(item) {
                            // Logic hiển thị chi tiết item
                            console.log("Showing details for item:", item);
                            // Mở modal hoặc thực hiện hành động khác để hiển thị chi tiết
                    },
                    //controller: db,
                    //data:clients,
                    data: @json($db),     
                    
                    controller: {
                        loadData: function(filter) {
                            // Lọc dữ liệu theo các tiêu chí tìm kiếm
                        
                            if((typeof filter) === "string" && filter!=='') {
                            return  filteredData = @json($db).filter(item => {
                                    return (item.name.includes(filter))
                                });

                            }

                            console.log('loadData:',filter);
                            //console.log('excelData:',this.excelData);
                            
                            filteredData =null
                            if(filter.name !=="" || filter.age!==undefined || filter.address !=="" || filter.country_id !==0 ){
                                //console.log(@json($db));
                            // console.log('filteredData');
                                    filteredData = @json($db).filter(item => {
                                    return (!filter.name || item.name.includes(filter.name)) && (!filter.age || item.age === filter.age) && (!filter.address || item.address.includes(filter.address)) && (!filter.country_id || item.country_id === filter.country_id)
                                });
                            }
                        
                            // && ( item.married === filter.married)
                            if(filter.married!==undefined ){
                                filteredData = @json($db).filter(item => {
                                    return (item.married === filter.married) && (!filter.name || item.name.includes(filter.name)) && (!filter.age || item.age === filter.age) && (!filter.address || item.address.includes(filter.address)) && (!filter.country_id || item.country_id === filter.country_id)
                                })                             
                            }

                            return filteredData ?? @json($db);
                            
                        },
                        insertItem: function(item) {
                            @this.addData(item);
                        },
                        updateItem: function(item) {
                            @this.updateData(item);
                        },
                        deleteItem: function(item) {
                            @this.deleteData(item.id);
                        },
                        
                    },

                    fields: [
                        {
                            headerTemplate: function() { 
                                return $("<input>").attr("type", "button").attr("class", "jsgrid-button jsgrid-delete-button")
                                        .on("click", function () {
                                            deleteSelectedItems();
                                        });
                            },
                            itemTemplate: function(_, item) {
                                return $("<input>").attr("type", "checkbox").attr("class", "row-checkbox").attr("data-id", `${item.id}`)
                                        .prop("checked", $.inArray(item, selectedItems) > -1)
                                        .on("change", function () {
                                            $(this).is(":checked") ? selectItem(item.id) : unselectItem(item.id);
                                        });
                            },
                            filterValue: function() {                             
                                    // Chọn phần tử checkbox đã cho
                                var $checkbox = $('input[type="checkbox"]').first(); // Chọn checkbox có thuộc tính readonly

                                // Kiểm tra xem phần tử đã có id chưa
                                if ($checkbox.length > 0) {
                                    // Nếu checkbox tồn tại
                                    if (!$checkbox.attr('id')) {
                                        // Nếu checkbox chưa có id, thêm id="selectAll"
                                        $checkbox.attr('id', 'selectAll');
                                    }

                                    // Xóa thuộc tính readonly
                                    $checkbox.removeAttr('readonly');

                                    //console.log('ID added and readonly removed.');
                                } else {
                                    //console.log('No checkbox found.');
                                }


                            },
                            type:"checkbox",                                          
                            align: "center",
                            width: 50,
                            sorting: false 
                        },        
                        { name: "name", type: "text", title:"Name" , width: 150, validate: "required", filtering: true  },
                        { 
                            name: "age", type: "number", width: 50, filtering: true,
                            validate: {
                                validator: "range",
                                message: function(value, item) {
                                    return "The client age should be between 18 and 99. Entered age is \"" + value + "\" is out of specified range.";
                                },
                                param: [18, 99]
                            }
                        },
                        { name: "address", type: "text", width: 200 ,  filtering: true},
                        { name: "country_id", type: "select", title:"Country" , items: @json($country), valueField: "id", textField: "name" , filtering: true},
                        { name: "married", type: "checkbox", title: "Is Married", sorting: true,  filtering: true },          
                        {
                            type: "control",     
                            modeSwitchButton: true,                           
                            editButton: true,                           
                            width: 150,
                            itemTemplate: function(value, item) {
                                // Tạo nút Detail
                            //var controlButton = $(".jsgrid-control-field")
                            // console.log(controlButton);
                            let {id,name,age} = item 
                                var editButton  = $("<input>")
                                    .attr("class", "jsgrid-button jsgrid-edit-button")
                                    .attr("type", "button")
                                    .attr("title", "edit")                                
                                    .on("click", function() {
                                        //editItem(item);
                                        //$("#jsGrid").jsGrid("editItem", item);
                                                                            
                                        let content = `<h5>${name} - ${age}</h5>`
                                        $('#myModal .modal-body').html(content)
                                        $store.modal.show = true
                                    });
                                var deleteButton   = $("<input>")
                                    .attr("class", "jsgrid-button jsgrid-delete-button mx-2")
                                    .attr("type", "button")
                                    .attr("title", "delete")                                
                                    .on("click", function() {
                                        @this.deleteData(id);
                                    });
                                var detailButton = $("<input>")
                                    .attr("class", "jsgrid-button jsgrid-detail-button")
                                    .attr("type", "button")
                                    .attr("title", "detail")                                
                                    .on("click", function() {
                                        // Logic hiển thị chi tiết ở đây
                                        //showDetail(item);
                                        console.log('Detail:',id);
                                    });
                                
                                // Trả về nút Edit và Detail
                                return $("<div>").append(editButton).append(detailButton).append(deleteButton);
                            }
                        }

                    ]
                });    
    
    });

   </script>
   
    @endscript
    