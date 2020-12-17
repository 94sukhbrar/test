(function () {
	"use strict";
 
    var getUrl = window.location;
    var BASEURL = "http://"+getUrl.hostname;//getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
   // console.log(getUrl.hostname);
    console.log(BASEURL);
 const EMPTY_CART =  "<tr id='cart_is_empty'> <td colspan='7'>  <h3> Your Cart is Empty.! Start adding item.</h3> </td> </tr>";
    $(function(){
        /**
         * geting the products by category
         * category click is below
         * @since 14-june-2019
         */
        $(".cat").on("click",function(){  
            ToggleLoader();  
            var id = $(this).attr('data-test');
            
            classRemover('btn-info');
            $(this).toggleClass("btn btn-info");
            $.ajax({
                    type: "POST",
                    url: BASEURL+"/product/all-product",
                    data: {id:id},
                    dataType: "json",
                    success: function(response){	
                        ToggleLoader(); 		
                       // console.log(response);
                        if(response.status == 200)
                            $("#prodects").html(response.details);
                        else   
                            $("#prodects").html("No Record Found");
                    }
            }); 
	
        });

    /**
     * Product click event handler 
     */
     $("#prodects").delegate(".add_prod", "click", function(){
        let prod_id = $(this).attr('data-id');
        AddProduct( prod_id );
    }); 

    function AddProduct(id) { 
        $("#cart_is_empty").addClass('hide');
         ToggleLoader();
       
            $.ajax({
                type: "POST",
                url: BASEURL+"/product/product-by-id",
                data: {id},
                success: function(response){
                

                    if(response.status ===200){
                        ToggleLoader(); 	
                        //will only add if item is not exist 
                        if( !isItemExist(id))	
                            $(".leadger").append(response.item);
                        //updating the repeated item
                        else{
                            //increasing quntity of item on repeative click
                            itrateItem(id,true);
                        }                            
                        $('#sub_total').text(CalculatePrice());
                    }else{
                        swal("Oops!", "Something went wrong", "error");
                    }
                    
                }
            });  
    }

    function itrateItem(id,flag) {
        let item_id= "#item_to_remove-"+id;
        let current_count = parseInt( $(item_id+"> td > .item_qty").text(), 10) ;
        if(flag){
            //incremnt by one  to item  
            current_count++;          
            $(item_id+"> td > .item_qty").text( current_count++);           
        }else{
            //decrement by one  to item
            current_count--;
            $(item_id+"> td > .item_qty").text( current_count--);
        }        

    }
    /**
     * Product decrement  event handler 
     */
    $(".table-borderd").delegate(".fa-minus", "click", function(){      
        if(  parseInt( $( "#item_to_remove-"+$(this).attr('data-id')+"> td > .item_qty " ).text() , 10)  > 1 )
            itrateItem($(this).attr('data-id'),false);
        $('#sub_total').text(CalculatePrice());
    }); 

     /**
     * Product Increment  event handler 
     */
    $(".table-borderd").delegate(".fa-plus", "click", function(){
        //if(  parseInt( $( "#item_to_remove-"+$(this).attr('data-id')+"> td > .item_qty " ).text() , 10)  > 1 )
        itrateItem($(this).attr('data-id'),true);
    $('#sub_total').text(CalculatePrice());
    }); 


    /**
     * 
     * @param {*} id 
     * @description check if item  is alredy exist into the cart
     * @returns  false | true
     */
    function isItemExist(id) {
        let item_id= "#item_to_remove-"+id;
        return  $(item_id).length > 0;
        

    }

    /**
     * 
     * @param {*} parentElementClass 
     * @param {*} classToRemove 
     * @since  7 July 2019
     * @description to remove the active class from the clicked category button will also help to any class form any elemnt from give parent 
     */
    function classRemover(classToRemove) {      
        $(".cat").removeClass(classToRemove);
      
    }

    /**
     * Remove item from cart Event Handler 
     */
    $(".leadger").delegate("td >.fa-times", "click", function(){
         
        RemoveItem( $(this ).attr('id'));
        $('#sub_total').text(CalculatePrice());
    }); 

    /**
     * Removing clicked item from cart
     */
    function RemoveItem(id) {
      
            
        removeItemFromRegister(id);
        $("#item_to_remove-"+id).remove();              
            if(isCartEmpty()){
                $("#cart_is_empty").removeClass('hide');
            }
          
            //swal("Done!", "Item is removed.", "warning");
    }   

    function isCartEmpty() {
            var rowCount = $('.leadger> tr').length;
             return rowCount > 1 ? false : true; 
    }
    
    /**
     * toggle loader
     * @since 14-june-2019
     */
    function ToggleLoader(){
        $('.loader').toggleClass('hide');
    }  

    /**
     * Recalulating the price on item add or remove or even the quantity is changed
     */
    function CalculatePrice() { 

        let the_item_total =0;
        $(".leadger tr").each(function() {
           //Skipping the cart is empty row
            if( !$(this).hasClass('hide') ){
                let qty = 1;
                let price = 0;
                $( "#" +  $(this).attr('id')+ ' > td').each(function(){
                    //filtring the price and quantity
                    if( $(this).children('span').hasClass('item_price'))
                        price = $(this).text();
                    if( $(this).children('span').hasClass('item_qty'))
                            qty = $(this).text();            
                });                
                the_item_total +=  qty * price;
            } 
        });
        return the_item_total; 
    }


    /**
     * Customer select or chnage event handler
     */
    $('#customer').on('change',function(){
        
            $.ajax({
                url: BASEURL + "/user/user-detail",
                type: "POST",
                data:{id: $(this).val() },
                success: function (response) {
                    console.log(response.details.contact_no  );
                    if(response.details.contact_no  == null){  
                        $(".customer_mobile").text( "NA");   
                        $('.customer_mobile').editable('setValue', null);                 
                    }else{                       
                       
                        $(".customer_mobile").text( response.details.contact_no);
                        $('.customer_mobile').editable('setValue', response.details.contact_no);
                    }

                    
                }
            });
        //alert( $(this).val() );
    }); 


    $('.cancel_order').on('click',function () {
        cancelOrder();
    });

    $(".calc").on('click',function(){
      let  text = ($(this).text()).replace(/\s/g, '');
      var myval  =  parseFloat( ($('.calborder').text()).trim().replace(/\s/g, '') ) + parseFloat( text.trim() );



console.log(   ($('.calborder').text()).trim().replace(/\s/g, '')    );
      switch (text.trim()) {
          case '1': 
          $('.calborder').text(myval);
              break;
          case '2': 
                $('.calborder').text(myval);
              break;
          case '3': 
                $('.calborder').text(myval);
              break;
          case '4': 
                $('.calborder').text(myval);
              break;
          case '5': 
                $('.calborder').text(myval);
              break;
          case '6': 
                $('.calborder').text(myval);
              break;
          case '7': 
                $('.calborder').text(myval);
              break;
          case '8': 
                $('.calborder').text(myval);
              break;
         case '9': 
                $('.calborder').text(myval);
              break;
        case '100': 
          $('.calborder').text(myval);
              break;
        case '200': 
              $('.calborder').text(myval);
               break;
        case '300': 
                  $('.calborder').text(myval);
              break;
        case '1000': 
             $('.calborder').text(myval);
              break;
          case '0': 
                $('.calborder').text(myval);
              break;
          case '.':  
          console.log(((myval).toString() ).indexOf("."));
          if(  ((myval).toString() ).indexOf(".") != '-1' )
                $('.calborder').append(".");
                
              break; 

          default:
                $('.calborder').text('0');
              break;
      }
        
    });


    
    //Selecting the payment method
    $(".paymath").on('click',function(){        
        $("#payment_method").val( $(this).attr('data-id') );
        let selected_payment_method =  parseInt($(this).attr('data-id'),10);

        $(".payment_buttons > button").each(function (ind,elemnt) {
            $(elemnt).addClass('btn-success');
            $(this).removeClass('btn-danger');
        });

        //Gift Voucher (4)
        if( selected_payment_method == 4){    
            
            
        }
        //Checque (1)
        if( selected_payment_method == 1){
            Swal.fire({
                title: 'Enter your cheque number',
                input: 'text',
                inputAttributes: {
                  autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Done',
                showLoaderOnConfirm: true,
                preConfirm: (note) => {
                  //get your note here
                  $("#cheque_no").val(note);
                   // $('.order_note').val(note);
        
                },
                allowOutsideClick: () => !Swal.isLoading()
              }).then((result) => {
                   console.log(result.dismiss);
                  //on Cancel Press
                  if(result.dismiss =='cancel' || result.dismiss =='backdrop'){
                     $(this).removeClass('btn-danger');
                     $("#payment_method").val("-1");
                  }
                 
              })

        }

        $(this).addClass('btn-danger');


    });  

    function getGst(type){
		
        $.ajax({
               url: BaseUrl + "ongoingorder/get-gst?type="+type,
               type: "POST",
               success: function (response) {
                   if(type == 0 ){
                       $("#tax_gst").val(response)
                       console.log("GST"+response);
                   }if(type == 2 ){
                       console.log("HST"+response);
                       $("#tax_hst").val(response)
                   }
               }
       });

    }
    function cancelOrder() {
        $.ajax({
            url: BASEURL + "/ongoingorder/cancel-order",
            type: "POST",
            success: function (response) {
               
                resetRegister();
                 //Do stop loading here after removing all data from ongoing order table
               // showHideLoading();

            }

        });

    }
    function removeItemFromRegister(id) {
        $.ajax({
            url: BASEURL + "/ongoingorder/delete-item/" + id,
            type: "POST",
            success: function (response) {}
        });

    }
    }); 
    function resetRegister() {
        //showing cart empty
       $("#cart_is_empty").removeClass('hide');       
        $(".leadger > tr").each(function () {   
            //Comparing the id if same as the cart_is_empty then not to remove else has to
            if( $(this).attr('id') !="cart_is_empty"){
                //removing item by item
               $("#"+ $(this).attr('id')).remove();
            }            
        });
        //Setting balance to zero
        $("#sub_total").text('0');
        $("#payment_method").val("-1");
        PaymentButtonRest();
    }
    /**
     * @description Reset the last used payment methos, it will remove the btn-danger class and add btn-success
     * @since 15 june 2019
     */
    function PaymentButtonRest() {
        $(".payment_buttons > button").each(function (ind,elemnt) {
            $(elemnt).addClass('btn-success');
            $(elemnt).removeClass('btn-danger');           
        });
    }
    $(".ticket_note").on('click',function () {
         Swal.fire({
        title: 'Enter your note',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Done',
        showLoaderOnConfirm: true,
        preConfirm: (note) => {
          //get your note here
            
            $('.order_note').val(note);

        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        
      })
    });
    //setting customer mobile numer to editable
    $('.customer_mobile').editable();
    //updating mobile number of the customer on save
    $('.customer_mobile').on('save', function(e, params) {
         $.ajax({
            url: BASEURL + "/user/update-mobile",
            type: "POST",
            data:{customer_mobile: params.newValue ,customer_id: $('#customer').val()},
            success: function (response) {}
        }); 
    });


    // complete  order | place order 
    $(".complete_order").on('click',function () {
        let data ={
            customer_id:    $('#customer').val(),
            paying_amount:  $('.calborder').text().trim(),
            total_amount: $('#sub_total').text() ,
            discount_amount:  0,
            discount_type:  0,
            order_note:$(".order_note").val() ,
            payment_method: $('#payment_method').val(),
            cheque_no: $("#cheque_no").val(),
            gift_card_no: $("#gift_card_no").val()
        };   
        
        
        if(  $("#payment_method").val() == -1){
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Please select payment method',
               
              });
        }else if( parseInt( $("#sub_total").text(),10) < 1 ){
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Add items to cart',
               
              });
        }else if( ! $("#customer").val() ){
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: 'Customer not selected',
               
              });
        } else
            $.ajax({
                url: BASEURL + "/order/place-order",
                type: "POST",
                data: data,
                success: function (response) {
                    resetRegister();
                    $(".due").text( response.due);
                    $('.customer_balance').text(response.customer_balance);
                    $('.amount_tendred').text(response.amount_tendred);
                    setTimeout(function(){
                        $(".due").text("0");
                        $('.customer_balance').text("0");
                        $('.amount_tendred').text("0");
                    }, 5000);

                }
            });

    });

 
})(jQuery);