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
        currency: 1,
        rate: [''],
        discount: 0,
        discount_string: 0,
        shipping: '0',
        shipping_string: '0',
        returns: 0,
        grand_total: 0,
    },

    methods:{
        init() {
            this.get_rate();
        },
        add_item() {
            axios.get('/get_first_product')
                .then(response => {
                    let tax_name = (response.data.tax) ? response.data.tax.name : ''
                    let tax_rate = (response.data.tax) ? response.data.tax.rate : 0

                    let cost1 = response.data.cost * this.rate[1] / this.rate[response.data.currency_id]
                    let cost2 = response.data.cost * this.rate[2] / this.rate[response.data.currency_id]
                    let cost3 = response.data.cost * this.rate[3] / this.rate[response.data.currency_id]
                    let cost  = response.data.cost * this.rate[this.currency] / this.rate[response.data.currency_id]
                    
                    this.order_items.push({
                        product_id: response.data.id,
                        product_name_code: response.data.name + "(" + response.data.code + ")",
                        cost: cost.toFixed(2),
                        cost1: cost1.toFixed(2),
                        cost2: cost2.toFixed(2),
                        cost3: cost3.toFixed(2),
                        currency: response.data.currency_id,
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
                let sub_total = (data[i].cost + (data[i].cost*data[i].tax_rate)/100) * data[i].quantity;
                this.order_items[i].sub_total = sub_total.toFixed(2);
                total_quantity += parseInt(data[i].quantity)
                total_cost += parseFloat(data[i].sub_total)
            }
            this.total.quantity = total_quantity
            this.total.cost = parseFloat(total_cost)
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
        },
        get_rate() {
            axios.get('/get_rate')
                .then(response => {
                    this.rate.push(response.data[0].rate);
                    this.rate.push(response.data[1].rate);
                    this.rate.push(response.data[2].rate);
                })
                .catch(error => {
                    console.log(error);
                });
        },
        convert(currency, amount) {
            let self_rate = this.rate[this.currency];
            let rate = this.rate[currency];
            let converted_rate = (amount * self_rate) /rate;
            return converted_rate.toFixed(2);
        },
        convert_currnecy() {
            data = this.order_items
            for(let i = 0; i < data.length; i++) {
                new_cost = 0;
                if(this.currency == 1) {
                    new_cost = data[i].cost1
                }else if(this.currency == 2) {
                    new_cost = data[i].cost2
                }else if(this.currency == 3) {
                    new_cost = data[i].cost3
                }
                this.order_items[i].cost = new_cost
            }
        }

    },
    filters: {
        currency: function (value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    },

    mounted:function() {
        this.init();
        this.add_item()
        $("#page").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_discount_shipping()
        this.calc_grand_total()
        let app = this
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
                                    currency_id: item.currency_id ,
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
                let cost1 = ui.item.cost * app.rate[1] / app.rate[ui.item.currency_id]
                let cost2 = ui.item.cost * app.rate[2] / app.rate[ui.item.currency_id]
                let cost3 = ui.item.cost * app.rate[3] / app.rate[ui.item.currency_id]
                let cost  = ui.item.cost * app.rate[app.currency] / app.rate[ui.item.currency_id]

                app.order_items[index].product_id = ui.item.id
                app.order_items[index].product_name_code = ui.item.label
                app.order_items[index].currency = ui.item.currency_id,
                app.order_items[index].cost = cost.toFixed(2),
                app.order_items[index].cost1 = cost1.toFixed(2),
                app.order_items[index].cost2 = cost2.toFixed(2),
                app.order_items[index].cost3 = cost3.toFixed(2),
                app.order_items[index].tax_name = ui.item.tax_name
                app.order_items[index].tax_rate = ui.item.tax_rate
                app.order_items[index].quantity = 1
                app.order_items[index].sub_total = 0
            }
        });
    }    
});


