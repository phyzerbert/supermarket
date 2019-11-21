var app = new Vue({
    el: '#page',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            discount: 0,
            cost: 0
        },
        grand_total: 0,
    },

    methods:{
        init() {
            // axios.get('/get_products')
            //     .then(response => {
            //         this.products = response.data;
            //     })
            //     .catch(error => {
            //         console.log(error);
            //     });
        },
        add_item() {
            axios.get('/get_first_product')
                .then(response => {
                    this.order_items.push({
                        product_id: response.data.id,
                        product_name_code: response.data.name + "(" + response.data.code + ")",
                        cost: response.data.cost,
                        discount: 0,
                        discount_string: 0,
                        quantity: 1,
                        expiry_date: "",
                        sub_total: 0,
                    })
                    Vue.nextTick(function() {
                        app.$refs['product'][app.$refs['product'].length - 1].select()
                    });
                })
                .catch(error => {
                    console.log(error);
                });            
        },
        calc_subtotal() {
            data = this.order_items
            // console.log(data)
            let total_discount = 0;
            let total_cost = 0;
            let reg_patt1 = /^\d+(?:\.\d+)?%$/
            let reg_patt2 = /^\d+$/

            for(let i = 0; i < data.length; i++) {

                if(reg_patt1.test(data[i].discount_string)){
                    this.order_items[i].discount = data[i].cost*parseFloat(data[i].discount_string)/100
                    // console.log(this.discount)
                }else if(reg_patt2.test(data[i].discount_string)){
                    this.order_items[i].discount = data[i].discount_string
                }else if(data[i].discount_string == ''){
                    this.order_items[i].discount = 0
                }else {
                    this.order_items[i].discount_string = '0';
                }

                this.order_items[i].sub_total = (parseInt(data[i].cost) - parseInt(data[i].discount)) * data[i].quantity
                total_discount += parseInt(data[i].discount) * data[i].quantity
                total_cost += data[i].sub_total
            }
            this.total.discount = total_discount
            this.total.cost = total_cost
        },
        calc_grand_total() {
            this.grand_total = this.total.cost
        },
        remove(i) {
            this.order_items.splice(i, 1)
        },
        formatPrice(value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        new_product() {
            $("#ajax-loading").show();
            var form = $('#create_product_form')[0];
            var formData = new FormData(form);
            formData.append("fileObj", $("#product_image")[0].files[0]);
            axios.post('/product/ajax_create', formData)
                .then(response => {
                    $("#ajax-loading").hide();
                    if(response.data.id != null) {                        
                        this.order_items.push({
                            product_id: response.data.id,
                            product_name_code: response.data.name + "(" + response.data.code + ")",
                            cost: response.data.cost,
                            discount: 0,
                            discount_string: 0,
                            quantity: 1,
                            sub_total: 0,
                        })
                    }else{
                        alert("Something went wrong");
                    }
                    $("#addProductModal").modal('hide');
                })
                .catch(error => {
                    $("#ajax-loading").hide();
                    if(error.response.status == 422) {
                        let messages = error.response.data.errors;
                        if(messages.name) {
                            $('#product_name_error strong').text(messages.name[0]);
                            $('#product_name_error').show();
                            $('#product_create_form .name').focus();
                        }
                        
                        if(messages.code) {
                            $('#product_code_error strong').text(messages.code[0]);
                            $('#product_code_error').show();
                            $('#create_form .code').focus();
                        }

                        if(messages.unit) {
                            $('#product_unit_error strong').text(messages.unit[0]);
                            $('#product_unit_error').show();
                            $('#product_create_form .unit').focus();
                        }

                        if(messages.cost) {
                            $('#product_cost_error strong').text(messages.cost[0]);
                            $('#product_cost_error').show();
                            $('#product_create_form .cost').focus();
                        }
                    }

                });
        }
    },

    mounted:function() {
        this.init();
        this.add_item()
        $("#page").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_grand_total()
        $(".product").autocomplete({
            source : function( request, response ) {
                axios.post('/get_autocomplete_products', { keyword : request.term })
                    .then(resp => {
                        // response(resp.data);
                        response(
                            $.map(resp.data, function(item) {
                                return {
                                    label: item.name + "(" + item.code + ")",
                                    value: item.name + "(" + item.code + ")",
                                    id: item.id,
                                    cost: item.cost,
                                }
                            })
                        );
                    })
                    .catch(error => {
                        console.log(error);
                    }
                );
            }, 
            minLength: 1,
            select: function( event, ui ) {
                let index = $(".product").index($(this));
                app.order_items[index].product_id = ui.item.id
                app.order_items[index].product_name_code = ui.item.label
                app.order_items[index].cost = ui.item.cost
                app.order_items[index].discount = 0
                app.order_items[index].quantity = 1
                app.order_items[index].sub_total = ui.item.cost
            }
        });
    },
    created: function() {
        var self = this
        $(document).keydown(function(e){
            // console.log(e.keyCode)
            if(e.keyCode == 21 || e.keyCode == 17 || e.keyCode == 25){
                self.add_item()
            }else if(e.keyCode == 16){
                if($("#addProductModal").hasClass("show")){
                    $("#addProductModal").modal('hide');
                } else {
                    $("#addProductModal").modal();
                }                
            }
        });
    }
});


