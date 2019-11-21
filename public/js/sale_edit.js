
var app = new Vue({
    el: '#page',

    data: {
        order_items: [],
        products: [],
        selected_product: '',
        total: {
            quantity: 0,
            price: 0
        },
        params: {
            type: 'sale',
            id: $('#sale_id').val()
        },
        grand_total: 0
    },


    methods:{
        init() {           
        },
        get_product(i) {
            const data = new FormData();
            data.append('id', this.order_items[i].product_id);

            axios.post('/get_product', data)
                .then(response => {
                    let tax_name = (response.data.tax) ? response.data.tax.name : ''
                    let tax_rate = (response.data.tax) ? response.data.tax.rate : 0
                    this.order_items[i].price = response.data.price
                    this.order_items[i].tax_name = tax_name
                    this.order_items[i].tax_rate = tax_rate
                    this.order_items[i].quantity = 1
                    this.order_items[i].sub_total = response.data.price + (response.data.price*response.data.tax.rate)/100
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
                        price: response.data.price ? response.data.price : 0,
                        tax_name: tax_name,
                        tax_rate: tax_rate,
                        quantity: 0,
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
            let total_price = 0;
            for(let i = 0; i < data.length; i++) {
                this.order_items[i].sub_total = (parseInt(data[i].price) + (data[i].price*data[i].tax_rate)/100) * data[i].quantity
                total_quantity += parseInt(data[i].quantity)
                total_price += data[i].sub_total
            }

            this.total.quantity = total_quantity
            this.total.price = total_price
        },
        calc_grand_total() {
            this.grand_total = this.total.price
        },
        remove(i) {
            this.order_items.splice(i, 1)
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
                            price: element.price,
                            tax_name: tax_name,
                            tax_rate: tax_rate,
                            quantity: element.quantity,
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
        this.calc_grand_total()
        $(".product").autocomplete({
            source : function( request, response ) {
                axios.post('/get_autocomplete_products', { keyword : request.term })
                    .then(resp => {
                        response(
                            $.map(resp.data, function(item) {
                                return {
                                    label: item.name + "(" + item.code + ")",
                                    value: item.name + "(" + item.code + ")",
                                    id: item.id,
                                    price: item.price ? item.price : 0,
                                    tax_name: item.tax.name,
                                    tax_rate: item.tax.rate,
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
                app.order_items[index].price = ui.item.price
                app.order_items[index].tax_name = ui.item.tax_name
                app.order_items[index].tax_rate = ui.item.tax_rate
                app.order_items[index].quantity = 1
                app.order_items[index].sub_total = ui.item.cost + (ui.item.cost*ui.item.tax_rate)/100
            }
        });
    }
});
