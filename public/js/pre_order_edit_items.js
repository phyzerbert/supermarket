
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
        params: {
            id: $('#order_id').val()
        }
    },
    methods:{
        init() {
            axios.get('/get_products')
                .then(response => {
                    this.products = response.data;
                })
                .catch(error => {
                    console.log(error);
                });  
                
            axios.post('/get_pre_order',this.params)
                .then(response => {
                    for (let i = 0; i < response.data.items.length; i++) {
                        const element = response.data.items[i];
                        axios.post('/get_product', {id:element.product_id})
                        .then(response1 => {
                            this.order_items.push({
                                product_id: element.product_id,
                                product_name_code: response1.data.name + "(" + response1.data.code + ")",
                                cost: element.cost,
                                discount: element.discount,
                                discount_string: element.discount_string,
                                quantity: element.quantity,
                                sub_total: element.subtotal,
                                item_id: element.id,
                            })                           
                        })
                        .catch(error => {
                            console.log(error);
                        });                    
                    }
                })
                .catch(error => {
                    console.log(error);
                }); 
        },
        get_product(i) {
            const data = new FormData();
            data.append('id', this.order_items[i].product_id);

            axios.post('/get_product', data)
                .then(response => {
                    let tax_name = (response.data.tax) ? response.data.tax.name : ''
                    let tax_rate = (response.data.tax) ? response.data.tax.rate : 0
                    this.order_items[i].cost = response.data.cost
                    this.order_items[i].tax_name = tax_name
                    this.order_items[i].tax_rate = tax_rate
                    this.order_items[i].quantity = 1
                    this.order_items[i].discount_string = 0
                    this.order_items[i].sub_total = response.data.cost
                })
                .catch(error => {
                    console.log(error);
                });
        },
        add_item() {
            this.order_items.push({
                product_id: "",
                product_name_code: "",
                cost: 0,
                discount: 0,
                discount_string: 0,
                quantity: 0,
                sub_total: 0,
            })            
        },
        calc_subtotal() {
            data = this.order_items
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
        formatPrice(value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        remove(i) {
            this.order_items.splice(i, 1)
        }
    },

    mounted:function() {
        this.init();        
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
            console.log(e.keyCode)
            if(e.keyCode == 21 || e.keyCode == 17 || e.keyCode == 25){
                self.add_item()
            }
        });
    }
});
