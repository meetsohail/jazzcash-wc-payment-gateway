<div class="ElementsModal--payment-details">
    <div class="ElementsModal--payment-form">
        <div class="form-row">
            <div class="ElementsModal--forms">
                <div id="payment-request-section" class="StripeElement--payment-request" style="display: none;">
                    <div id="payment-request-button" class="StripeElement--payment-request-button"
                        style="display: none;">
                    </div>
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
                    <div id="card-errors" class="ElementsModal--error-message" role="alert"></div>
                </div>
                <div class="footer ElementsModal--footer-text">
                    By purchasing this, you agree to JazzCash
                    <a class="ElementsModal--footer-text" href="https://developer.jazzcash.com.pk/store/site/pages/terms&cond.jag">Terms and Conditions.</a>
                </div>
            </div>
        </div>
    </div>
</div>