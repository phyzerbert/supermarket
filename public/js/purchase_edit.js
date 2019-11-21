
var app = new Vue({
    el: '#page',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            quantity: 0,
            cost: 0
        },        
        discount: 0,
        discount_string: 0,
        shipping: 0,
        shipping_string: '0',
        returns: 0,
        grand_total: 0,
        params: {
            type: $('#data').data('type'),
            id: $('#data').data('id')
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
                
            axios.post('/get_data',this.params)
                .then(response => {
                    this.discount_string = response.data.discount_string
                    this.shipping_string = response.data.shipping_string
                    this.returns = response.data.returns
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
                    this.order_items[i].sub_total = response.data.cost + (response.data.cost*response.data.tax.rate)/100
                })
                .catch(error => {
                    console.log(error);
                });
        },
        add_item() {
            axios.get('/get_first_product')
                .then(response => {
                    let tax_name = (response.data.tax) ? response.data.tax.name : ''
                    let tax_rate = (response.data.tax) ? response.data.tax.rate : 0
                    this.order_items.push({
                        product_id: response.data.id,
                        product_name_code: response.data.name + "(" + response.data.code + ")",
                        cost: response.data.cost,
                        tax_name: tax_name,
                        tax_rate: tax_rate,
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
            let total_quantity = 0;
            let total_cost = 0;
            for(let i = 0; i < data.length; i++) {
                this.order_items[i].sub_total = (parseInt(data[i].cost) + (data[i].cost*data[i].tax_rate)/100) * data[i].quantity
                total_quantity += parseInt(data[i].quantity)
                total_cost += data[i].sub_total
            }

            this.total.quantity = total_quantity
            this.total.cost = total_cost
        },
        calc_grand_total() {
            this.grand_total = this.total.cost - this.discount - this.shipping - this.returns
        },
        calc_discount_shipping(){
            let reg_patt1 = /^\d+(?:\.\d+)?%$/
            let reg_patt2 = /^\d+$/
            if(reg_patt1.test(this.discount_string)){
                this.discount = this.total.cost*parseFloat(this.discount_string)/100
                // console.log(this.discount)
            }else if(reg_patt2.test(this.discount_string)){
                this.discount = this.discount_string
            }else if(this.discount_string == ''){
                this.discount = 0
            }else {
                this.discount_string = '0';
            }

            if(reg_patt1.test(this.shipping_string)){
                this.shipping = this.total.cost*parseFloat(this.shipping_string)/100
                // console.log("percent")
            }else if(reg_patt2.test(this.shipping_string)){
                this.shipping = this.shipping_string
            }else if(this.shipping_string == ''){
                this.shipping = 0
            }else {
                this.shipping_string = '0';
            }

        },
        remove(i) {
            this.order_items.splice(i, 1)
        },
        formatPrice(value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },

    filters: {
        currency: function (value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    },

    mounted:function() {
        this.init();
        axios.post('/get_orders', this.params)
            .then(response => {
                for (let i = 0; i < response.data.length; i++) {
                    const element = response.data[i];
                    axios.post('/get_product', {id:element.product_id})
                    .then(response1 => {
                        let tax_name = (response1.data.tax) ? response1.data.tax.name : ''
                        let tax_rate = (response1.data.tax) ? response1.data.tax.rate : 0
                        this.order_items.push({
                            product_id: element.product_id,
                            product_name_code: response1.data.name + "(" + response1.data.code + ")",
                            cost: element.cost,
                            tax_name: tax_name,
                            tax_rate: tax_rate,
                            quantity: element.quantity,
                            expiry_date: element.expiry_date,
                            sub_total: element.subtotal,
                            order_id: element.id,
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
        
        $("#page").css('opacity', 1);
            
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_discount_shipping()
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
                                    tax_name: item.tax ? item.tax.name : '',
                                    tax_rate: item.tax ? item.tax.rate : 0,
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
                app.order_items[index].tax_name = ui.item.tax_name
                app.order_items[index].tax_rate = ui.item.tax_rate
                app.order_items[index].quantity = 1
                app.order_items[index].sub_total = ui.item.cost + (ui.item.cost*ui.item.tax_rate)/100
            }
        });
    }
});
