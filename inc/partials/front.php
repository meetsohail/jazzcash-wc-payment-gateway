<style>
    .demo {
        background: #ffffff;
        font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        text-align: center;
        margin-top: 55px;
    }
    .jazzcash_loader {
  border: 16px solid #f3f3f3; /* Light grey */
  border-top: 5px solid #3498db; /* Blue */
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;
  margin: auto;
}


@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

    .replay {
        color: rgb(0, 102, 240);
        font-size: 17px;
        font-weight: 500;
    }


    .product-name {
        text-align: left;
        color: rgb(0, 0, 0);
        font-size: 17px;
        font-weight: 500;
        opacity: 0.7;
    }

    .product-price {
        text-align: left;
        color: rgb(0, 0, 0);
        font-size: 28px;
        font-weight: 500;
    }

    .endstate {
        display: none;
    }

    .ElementsModal--modal {
        all: initial;
        box-sizing: border-box;
        position: fixed;
        font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        visibility: hidden;
        transform: scale(1.1);
        transition: visibility 0s linear 0.25s, opacity 0.25s 0s, transform 0.25s;
        z-index: 100001 !important;
    }

    .ElementsModal--modal-content {
        position: absolute;
        top: 42%;
        left: 50%;
        /* make media query for this :) */
        transform: translate(-50%, -50%);
        border-radius: 10px;
        background: rgb(255, 255, 255);
        overflow: hidden;
        width: 385px;
        border-radius: 0.5rem;
    }

    @media screen and (max-width: 600px) {
        .ElementsModal--modal-content {
            height: 100vh;
            width: 100%;
            border-radius: 0;
        }

        .ElementsModal--top {
            padding-top: 4em;
        }

        .ElementsModal--close {
            padding-top: 4em;
        }
    }

    .ElementsModal--top {
        display: flex;
        justify-content: flex-end;
        position: relative;
    }

    .ElementsModal--close {
        background: none;
        color: inherit;
        border: none;
        padding: 0;
        font: inherit;
        outline: inherit;
        color: rgb(255, 255, 255);
        cursor: pointer;
        position: absolute;
        top: 0;
        right: 0;
        border: none;
    }

    .ElementsModal--show-modal {
        opacity: 1;
        visibility: visible;
        transform: scale(1);
        transition: visibility 0s linear 0s, opacity 0.25s 0s, transform 0.25s;
    }

    .ElementsModal--details {
        margin-bottom: 2px;
    }

    .ElementsModal--price {
        color: rgb(255, 255, 255);
        font-size: 36px;
        font-weight: 600;
    }

    .ElementsModal--top-banner {
        background-color: black;
        text-align: center;
        background: rgb(0, 0, 0);
        padding: 1em;
        padding-top: 20px;
        padding-bottom: 2em;
    }

    .ElementsModal--email {
        color: rgba(255, 255, 255, 0.5);
        font-size: 16px;
        font-weight: 500;
    }

    .ElementsModal--product {
        color: rgba(255, 255, 255, 0.5);
        font-size: 16px;
        font-weight: 500;
    }

    .ElementsModal--company {
        color: rgb(255, 255, 255);
        font-size: 18px;
        font-weight: bold;
        margin: auto;
        margin-bottom: 32px;
    }

    .ElementsModal--footer-text {
        color: rgba(0, 0, 0, 0.4);
        font-size: 12px;
        font-weight: normal;
        text-align: center;
        line-height: 16px;
    }

    .ElementsModal--error-message {
        margin-top: 5px;
        color: rgb(220, 39, 39);
        font-size: 13px;
        line-height: 17px;
    }

    .ElementsModal--pay-button-wrapper {
        font-family: -apple-system, BlinkMacSystemFont, sans-serif;
        margin: 25px;
        width: 350px;
        height: 40px;
    }

    .ElementsModal--pay-button {
        cursor: pointer;
        border: 0;
        width: 100%;
        text-align: center;
        height: 40px;
        box-shadow: inset 0 0 0 1px rgba(50, 50, 93, 0.1),
            0 2px 5px 0 rgba(50, 50, 93, 0.1), 0 1px 1px 0 rgba(0, 0, 0, 0.07);
        border-radius: 6px 6px 6px 6px;
        font-size: 16px;
        font-weight: 600;

        background-color: rgb(0, 116, 212);
        color: rgb(255, 255, 255);
    }

    .ElementsModal--pay-button:focus {
        outline: none;
        box-shadow: 0 0 0 1px rgba(50, 151, 211, 0.3), 0 1px 1px 0 rgba(0, 0, 0, 0.07),
            0 0 0 4px rgba(50, 151, 211, 0.3);
    }

    .ElementsModal--dropdowns {
        margin: 10px;
        -webkit-appearance: none;
        background: rgb(255, 255, 255);
        box-shadow: 0px 0px 0px 1px rgb(224, 224, 224),
            0px 2px 4px 0px rgba(0, 0, 0, 0.07), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.05);
        border-radius: 4px 4px 4px 4px;
    }

    /* Form */

    .ElementsModal--payment-form {
        margin-bottom: 0;
    }

    .ElementsModal--label {
        color: rgba(0, 0, 0, 0.6);
        font-size: 14px;
        font-weight: 500;
    }

    .ElementsModal--forms {
        padding: 5%;
    }

    .ElementsModal--form {
        margin-bottom: 14px;
    }

    .ElementsModal--form-label {
        font-size: 13px;
        margin-bottom: 4px;
        display: block;
        color: rgba(0, 0, 0, 0.6);
    }

    .ElementsModal--form-select select {
        padding: 10px 12px;
        width: 100%;
        border: 1px solid transparent;
        outline: none;
        box-shadow: 0px 0px 0px 1px rgb(224, 224, 224),
            0px 2px 4px 0px rgba(0, 0, 0, 0.07), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.05);
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
        border-radius: 5px 5px 5px 5px;

        background-color: white;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M10.193 3.97a.75.75 0 0 1 1.062 1.062L6.53 9.756a.75.75 0 0 1-1.06 0L.745 5.032A.75.75 0 0 1 1.807 3.97L6 8.163l4.193-4.193z' fill='%23000' fill-rule='evenodd' fill-opacity='.4'/%3E%3C/svg%3E");
        background-size: 12px;
        background-position: calc(100% - 16px) center;
        background-repeat: no-repeat;
        color: rgb(40, 40, 40);
        font-size: 16px;
        font-weight: normal;
    }

    .ElementsModal--form-select input {
        padding: 10px 12px;
        width: 100%;
        border: 1px solid transparent;
        outline: none;
        box-shadow: 0px 0px 0px 1px rgb(224, 224, 224),
            0px 2px 4px 0px rgba(0, 0, 0, 0.07), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.05);
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
        border-radius: 5px 5px 5px 5px;

        background-color: white;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        color: rgb(40, 40, 40);
        font-size: 16px;
        font-weight: normal;
    }

    .ElementsModal--form-select select:-moz-focusring {
        color: transparent;
        text-shadow: 0 0 0 rgb(0, 0, 0);
    }

    .ElementsModal--form-select select:focus {
        box-shadow: 0 0 0 1px rgba(50, 151, 211, 0.3), 0 1px 1px 0 rgba(0, 0, 0, 0.07),
            0 0 0 4px rgba(50, 151, 211, 0.3);
    }

    .ElementsModal--form-select select::-ms-expand {
        display: none;
        /* hide the default arrow in ie10 and ie11 */
    }

    .ElementsModal--form-divider {
        margin-top: 14px;
        margin-bottom: 25px;
        text-align: center;
        border-bottom: 1px solid rgb(231, 231, 231);
        height: 20px;
        width: 100%;
    }

    .ElementsModal--form-divider-text {
        position: relative;
        bottom: -10px;
        /* half of line-height */
        padding: 0 10px;
        background: rgb(255, 255, 255);
        color: rgb(144, 144, 144);
        font-size: 14px;
        font-weight: 400;
    }

    .StripeElement--payment-request {
        display: none;
        margin-bottom: 14px;
    }

    .StripeElement--payment-request-button {
        margin-bottom: 12px;
    }

    .StripeElement--card {
        box-sizing: border-box;
        height: 40px;
        padding: 10px 12px;
        border: 1px solid transparent;
        border-radius: 5px 5px 5px 5px;
        background-color: white;

        box-shadow: 0px 0px 0px 1px rgb(224, 224, 224),
            0px 2px 4px 0px rgba(0, 0, 0, 0.07), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.05);
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }

    .StripeElement--card--focus {
        box-shadow: 0 1px 3px 0 rgb(207, 215, 223);
    }

    .StripeElement--card--invalid {
        border-color: rgb(239, 152, 150);
    }

    .StripeElement--card--webkit-autofill {
        background-color: rgb(254, 253, 229) !important;
    }
</style>
<div class="ElementsModal--payment-details">
    <div class="ElementsModal--payment-form">
        <div class="form-row">
            <div class="ElementsModal--forms">
                <div id="payment-request-section" class="StripeElement--payment-request" style="display: none;">
                    <div id="payment-request-button" class="StripeElement--payment-request-button"
                        style="display: none;">
                        <!-- A Stripe Element will be inserted here. -->
                    </div>
                    <!-- Used to display form errors. -->
                    <div id="paymentRequest-errors" class="ElementsModal--error-message" role="alert"></div>
                    <div class="ElementsModal--form-divider">
                        <span class="ElementsModal--form-divider-text">Or pay with card</span>
                    </div>
                </div>
                <div class="ElementsModal--form">
                    <label class="text">
                        <span class="ElementsModal--form-label spacer">Phone Number</span>
                        <div id="phone_number" class="ElementsModal--form-select">
                            <input type="number" name="phone_number" autocomplete="phone number" aria-label="JazzCash Account Phone Number">
                        </div>
                    </label>
                    <!-- Used to display form errors. -->
                    <div id="card-errors" class="ElementsModal--error-message" role="alert"></div>
                </div>
                <div class="ElementsModal--form">
                    <label class="text">
                        <span class="ElementsModal--form-label spacer">Cnic <small>(last 6 digits of
                                CNIC)</small></span>
                        <div id="cnic" class="ElementsModal--form-select">
                            <input type="number" maxlength="6" name="cnic" autocomplete="cnic" aria-label="Cnic">
                        </div>
                    </label>
                    <!-- Used to display form errors. -->
                    <div id="card-errors" class="ElementsModal--error-message" role="alert"></div>
                </div>
                <!-- Edit your terms and conditions here   -->
                <div class="footer ElementsModal--footer-text">
                    By purchasing this, you agree to JazzCash
                    <a class="ElementsModal--footer-text" href="wordpress">Terms and Conditions.</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // jQuery(document).ready(function(){
    //     jQuery('#payment_method_jazzcash-wc-payment-gateway').on('change', function(){
            
    //     }); 
    // });
</script>