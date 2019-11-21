var app = new Vue({
    el: '#page',

    data: {
        order_items: [],
        checked_items: [],
        filtered_items: [],
        products: [],
        total: {
            discount: 0,
            cost: 0
        },
        params: {
            id: $('#order_id').val()
        },
        discount: 0,
        discount_string: 0,
        shipping: '0',
        shipping_string: '0',
        returns: 0,
        grand_total: 0,
        keyword : '',
    },

    methods:{
        init() {
            axios.post('/get_pre_order',this.params)
                .then(response => {
                    for (let i = 0; i < response.data.items.length; i++) {
                        const element = response.data.items[i];
                        axios.post('/get_product', {id:element.product_id})
                            .then(response1 => {
                                axios.post('/get_received_quantity', {id:element.id})
                                    .then(response2 => {
                                        this.order_items.push({
                                            product_id: element.product_id,
                                            product_code: response1.data.code,
                                            product_name: response1.data.name,
                                            cost: element.cost,
                                            discount: element.discount,
                                            discount_string: element.discount_string,
                                            ordered_quantity: element.quantity,
                                            received_quantity: response2.data,
                                            balance: element.quantity - response2.data,
                                            receive_quantity: element.quantity - response2.data,
                                            sub_total: element.subtotal,
                                            item_id: element.id,
                                        })
                                        
                                    })
                                    .catch(error => {
                                        console.log(error);
                                    });
                            })
                            .catch(error => {
                                console.log(error);
                            });                    
                    }

                    Vue.nextTick(function() {
                        this.filtered_items = this.order_items
                    });
                })
                .catch(error => {
                    console.log(error);
                }); 
        },
        calc_subtotal() {
            data = this.order_items
            let total_discount = 0;
            let total_cost = 0;

            for(let i = 0; i < data.length; i++) {
                if(this.checked_items.indexOf(data[i].item_id) == -1) continue;
                this.order_items[i].sub_total = (parseInt(data[i].cost) - parseInt(data[i].discount)) * data[i].receive_quantity
                total_discount += parseInt(data[i].discount) * data[i].receive_quantity
                total_cost += data[i].sub_total
            }
            this.total.discount = total_discount
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
        formatPrice(value) {
            let val = value;
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        searchProduct() {
            const keyword = this.keyword;
            let data = this.order_items
            this.filtered_items = []
            for(let i = 0; i < data.length; i++) {
                if((data[i].product_name.indexOf(keyword) == -1) && (data[i].product_code.indexOf(keyword) == -1)) continue;
                this.filtered_items.push(data[i])
            }
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
        $("#page").css('opacity', 1);
    },
    updated: function() {
        this.calc_subtotal()
        this.calc_discount_shipping()
        this.calc_grand_total()
    },
    created: function() {
        this.filtered_items = this.order_items
    }
});


