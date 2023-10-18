// document.querySelectorAll('.add-to-cart').forEach(button => {
//     button.addEventListener('click', function() {
//         const productId = this.getAttribute('data-product-id');
        
//         // 发送Ajax请求将产品添加到购物篮
//         // 你需要在服务器端编写相应的控制器来处理这个请求
//         // 以下是一个示例Ajax请求使用jQuery的方式：
//         $.ajax({
//             url: '/panier/' + productId,
//             method: 'POST',
//             success: function(response) {
//                 // 处理成功响应，例如更新购物篮数量
//                 const cartItemCountElement = document.getElementById('cart-item-count');
//                 if (cartItemCountElement) {
//                     cartItemCountElement.textContent = response.cartItemCount;
//                 }
//                 // 提示用户产品已成功添加到购物篮
//                 alert(response.message);
//             },
//             error: function(error) {
//                 // 处理错误
//                 alert('An error occurred while adding the product to the cart.');
//             }
//         });
//     });
// });