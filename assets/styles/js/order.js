// si le environement === dev,on va utiliser le clé de pulbic
// quand le lien de js fonctionne , il faut d'abord ajout clé

let stripe = Stripe('pk_test_51NxTN4AViwvcrKHvLyrYTylmyAmYqBn7PHbAPANVJzGgUGBh6GHz8OxNgtFAuoY6L4edEs07JiMsb5WpC3ptY6CI001VPSfV7w');


// foncution elements qui associer stripe à un formulaire #
// 通过Stripe Elements，创建了一个用于输入信用卡信息的部分。Stripe Elements是Stripe提供的用于创建自定义支付表单的工具，以确保信用卡信息的安全输入。这个部分通过 card.mount('#card-elements') 与Stripe Elements绑定。
let elements = stripe.elements();
// récuprer l 'id de produits
let productElement = document.getElementById('product');
let subscription = productElement.getAttribute('data-id');


// dans le controller on a passé intentsecret #
let clientSecretElement = document.getElementById('clientSecret');
let clientSecret = clientSecretElement.getAttribute('data-id');
console.log('Result paymentIntent', clientSecret);

// info sur le son payment,email / nom / prénom etc ...
let userElement = document.getElementById('username');
let cardholderName = userElement.getAttribute('data-id');


let cardholderEmailuserElement = document.getElementById('useremail');
let cardholderEmail = cardholderEmailuserElement.getAttribute('data-id');
console.log(cardholderName, cardholderEmail);



// diseign
let styleCustom = {
base: {
fontSize: '16px',
color: '#25332d'
}
}

// Monter notre form a l'objet stripe
let card = elements.create('card', {style: styleCustom});
// je la socier ici de id
card.mount('#card-elements');

// ecoute notre elements; et message error
card.addEventListener('change', e => {
let displayError = document.getElementById('card-errors');

if (e.error) {
displayError.textContent = e.error.message;
} else {
displayError.textContent = '';
}
});

// ici le id pour vérifier le status quand on post le formulaire
let form = document.getElementById('payment-form');
// quand on fait le submit il va recharger le page
// 指定了监听的事件类型为"submit"
form.addEventListener('submit', e => {
// par default => stopper le chargement
// event.preventDefault(); 是一个方法调用，它的作用是阻止事件的默认行为。在这种情况下，它防止表单的默认提交行为。
// 防止表单提交和页面刷新
e.preventDefault();
// 在表单提交事件的处理函数中，Stripe.js使用 stripe.handleCardPayment 函数来处理信用卡支付。它将客户的信用卡信息与支付意图（clientSecret）一起发送到Stripe服务器进行处理。如果支付成功，Stripe将返回支付意图的详细信息，并将它们传递给 stripeTokenHandler 函数进行后续处理。
stripe.handleCardPayment(clientSecret, card, {
// json vont récuprer le formation avec la card
// les infos vient de site de stripe
payment_method_data: {
billing_details: {
name: cardholderName,
email: cardholderEmail
}
}
}).then((result) => {
if (result.error) { // display error
} else if ('paymentIntent' in result) {
console.log('Result:', result);
stripeTokenHandler(result.paymentIntent);
console.log('Result paymentIntent:', result.paymentIntent);
}
})
});

// on va envoyer les informations dans le formulaire, cette function pour appelle de en haut de ligne 112 de function,on a passe le retoure de stripe-> après stripe va nous donner le result
// 这个函数用于处理从Stripe服务器返回的支付意图信息。它创建了一些隐式输入字段，并将支付意图的ID、支付方法、状态以及订阅信息传递给表单。然后，它触发表单的提交以将这些信息发送到服务器进行进一步处理。
function stripeTokenHandler(intent) {
let form = document.getElementById('payment-form');

let InputIntentId = document.createElement('input');
let InputIntentPaymentMethod = document.createElement('input');
let InputIntentStatus = document.createElement('input');
let InputSubscription = document.createElement('input');

InputIntentId.setAttribute('type', 'hidden');
InputIntentId.setAttribute('name', 'stripeIntentId');
    InputIntentId.setAttribute('value', intent.id);
    console.log(intent.id);

InputIntentPaymentMethod.setAttribute('type', 'hidden');
InputIntentPaymentMethod.setAttribute('name', 'stripeIntentPaymentMethod');
InputIntentPaymentMethod.setAttribute('value', intent.payment_method);


InputIntentStatus.setAttribute('type', 'hidden');
InputIntentStatus.setAttribute('name', 'stripeIntentStatus');
InputIntentStatus.setAttribute('value', intent.status);

InputSubscription.setAttribute('type', 'hidden');
InputSubscription.setAttribute('name', 'subscription');
InputSubscription.setAttribute('value', subscription);

form.appendChild(InputIntentId);
form.appendChild(InputIntentPaymentMethod);
form.appendChild(InputIntentStatus);
form.appendChild(InputSubscription);
    form.submit();
   
}

