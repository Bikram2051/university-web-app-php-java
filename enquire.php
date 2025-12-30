<?php
// Start session to preserve form data if validation fails
session_start();
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : array();
$errors = isset($_SESSION['form_errors']) ? $_SESSION['form_errors'] : array();
?>
<?php include 'header.inc'; ?>
        <h1>Product Enquiry</h1>

                <?php if (!empty($errors)): ?>
            <div class="error-container" style="display: block;">
                <div class="error-message">
                    <h4>Please correct the following errors:</h4>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <form id="enquiry-form" action="process_enquiry.php" method="post" novalidate>
            <fieldset class="personal-info">
                <legend>Personal Information</legend>

                <div class="form-group">
                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" 
                           value="<?php echo htmlspecialchars(isset($form_data['firstname']) ? $form_data['firstname'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" 
                           value="<?php echo htmlspecialchars(isset($form_data['lastname']) ? $form_data['lastname'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars(isset($form_data['email']) ? $form_data['email'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" placeholder="04XX XXX XXX"
                           value="<?php echo htmlspecialchars(isset($form_data['phone']) ? $form_data['phone'] : ''); ?>">
                </div>
            </fieldset>

            <fieldset class="address-info">
                <legend>Delivery Address</legend>

                <div class="form-group">
                    <label for="street">Street Address:</label>
                    <input type="text" id="street" name="street" 
                           value="<?php echo htmlspecialchars(isset($form_data['street']) ? $form_data['street'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label for="suburb">Suburb/Town:</label>
                    <input type="text" id="suburb" name="suburb" 
                           value="<?php echo htmlspecialchars(isset($form_data['suburb']) ? $form_data['suburb'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label for="state">State:</label>
                    <select id="state" name="state">
                        <option value="">Please select</option>
                        <option value="VIC" <?php echo (isset($form_data['state']) && $form_data['state'] == 'VIC') ? 'selected' : ''; ?>>Victoria</option>
                        <option value="NSW" <?php echo (isset($form_data['state']) && $form_data['state'] == 'NSW') ? 'selected' : ''; ?>>New South Wales</option>
                        <option value="QLD" <?php echo (isset($form_data['state']) && $form_data['state'] == 'QLD') ? 'selected' : ''; ?>>Queensland</option>
                        <option value="NT" <?php echo (isset($form_data['state']) && $form_data['state'] == 'NT') ? 'selected' : ''; ?>>Northern Territory</option>
                        <option value="WA" <?php echo (isset($form_data['state']) && $form_data['state'] == 'WA') ? 'selected' : ''; ?>>Western Australia</option>
                        <option value="SA" <?php echo (isset($form_data['state']) && $form_data['state'] == 'SA') ? 'selected' : ''; ?>>South Australia</option>
                        <option value="TAS" <?php echo (isset($form_data['state']) && $form_data['state'] == 'TAS') ? 'selected' : ''; ?>>Tasmania</option>
                        <option value="ACT" <?php echo (isset($form_data['state']) && $form_data['state'] == 'ACT') ? 'selected' : ''; ?>>Australian Capital Territory</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="postcode">Postcode:</label>
                    <input type="text" id="postcode" name="postcode" 
                           value="<?php echo htmlspecialchars(isset($form_data['postcode']) ? $form_data['postcode'] : ''); ?>">
                </div>
            </fieldset>

            <fieldset class="contact-preference">
                <legend>Preferred Contact Method</legend>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="contact_method" value="email" 
                               <?php echo (isset($form_data['contact_method']) && $form_data['contact_method'] == 'email') ? 'checked' : ''; ?>>
                        Email
                    </label>
                    <label>
                        <input type="radio" name="contact_method" value="phone"
                               <?php echo (isset($form_data['contact_method']) && $form_data['contact_method'] == 'phone') ? 'checked' : ''; ?>>
                        Phone
                    </label>
                    <label>
                        <input type="radio" name="contact_method" value="post"
                               <?php echo (isset($form_data['contact_method']) && $form_data['contact_method'] == 'post') ? 'checked' : ''; ?>>
                        Post
                    </label>
                </div>
            </fieldset>

            <fieldset class="product-info">
                <legend>Product Information</legend>

                <div class="form-group">
                    <label for="product">Product of Interest:</label>
                    <select id="product" name="product">
                        <option value="">Please select</option>
                        <option value="aurora" data-price="2499.00" 
                                <?php echo (isset($form_data['product']) && $form_data['product'] == 'aurora') ? 'selected' : ''; ?>>Aurora Series - $2,499.00</option>
                        <option value="nexus" data-price="1599.00"
                                <?php echo (isset($form_data['product']) && $form_data['product'] == 'nexus') ? 'selected' : ''; ?>>Nexus Series - $1,599.00</option>
                        <option value="essence" data-price="699.00"
                                <?php echo (isset($form_data['product']) && $form_data['product'] == 'essence') ? 'selected' : ''; ?>>Essence Series - $699.00</option>
                    </select>
                </div>

                <!-- Room Size Audio Planner -->
                <div class="form-group">
                    <label for="room-size">Room Size</label>
                    <select id="room-size" name="room_size">
                        <option value="">Select your room size</option>
                        <option value="small" <?php echo (isset($form_data['room_size']) && $form_data['room_size'] == 'small') ? 'selected' : ''; ?>>Small (≤ 15 m²)</option>
                        <option value="medium" <?php echo (isset($form_data['room_size']) && $form_data['room_size'] == 'medium') ? 'selected' : ''; ?>>Medium (16–25 m²)</option>
                        <option value="large" <?php echo (isset($form_data['room_size']) && $form_data['room_size'] == 'large') ? 'selected' : ''; ?>>Large (26+ m²)</option>
                    </select>
                </div>
                <div id="planner-reco" class="planner-reco" aria-live="polite"></div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input id="quantity" name="quantity" type="text" inputmode="numeric" placeholder="e.g., 1"
                           value="<?php echo htmlspecialchars(isset($form_data['quantity']) ? $form_data['quantity'] : ''); ?>">
                </div>

                <div class="form-group">
                    <label>Desired Features (Additional Costs):</label>
                    <div class="checkbox-group">
                        <?php
                        $selected_features = isset($form_data['features']) ? $form_data['features'] : array();
                        if (!is_array($selected_features)) $selected_features = array();
                        ?>
                        <label>
                            <input type="checkbox" name="features[]" value="4k_upscaling" data-price="200"
                                   <?php echo in_array('4k_upscaling', $selected_features) ? 'checked' : ''; ?>>
                            4K Upscaling (+$200)
                        </label>
                        <label>
                            <input type="checkbox" name="features[]" value="wireless_rear" data-price="150"
                                   <?php echo in_array('wireless_rear', $selected_features) ? 'checked' : ''; ?>>
                            Wireless Rear Speakers (+$150)
                        </label>
                        <label>
                            <input type="checkbox" name="features[]" value="smart_hub" data-price="100"
                                   <?php echo in_array('smart_hub', $selected_features) ? 'checked' : ''; ?>>
                            Smart Hub Integration (+$100)
                        </label>
                        <label>
                            <input type="checkbox" name="features[]" value="extended_warranty" data-price="300"
                                   <?php echo in_array('extended_warranty', $selected_features) ? 'checked' : ''; ?>>
                            Extended Warranty (+$300)
                        </label>
                        <label>
                            <input type="checkbox" name="features[]" value="professional_calibration" data-price="250"
                                   <?php echo in_array('professional_calibration', $selected_features) ? 'checked' : ''; ?>>
                            Professional Calibration (+$250)
                        </label>
                    </div>
                </div>

                <div class="price-summary">
                    <h4>Estimated Total: $<span id="estimated-total">0.00</span></h4>
                </div>
            </fieldset>

            <fieldset class="comments">
                <legend>Additional Comments</legend>
                <div class="form-group">
                    <label for="comments">Questions or Special Requirements:</label>
                    <textarea id="comments" name="comments" rows="5"
                        placeholder="Please let us know about any specific requirements or questions you have about our products..."><?php echo htmlspecialchars(isset($form_data['comments']) ? $form_data['comments'] : ''); ?></textarea>
                </div>
            </fieldset>

            <div class="form-submit">
                <button type="reset" class="reset-button">Reset Form</button>
                <button type="submit" id="pay-now-button" class="submit-button">Proceed to Payment</button>
            </div>
        </form>

        <script>
            // Initialize price calculator with saved values
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    if (document.getElementById('estimated-total')) {
                        const event = new Event('change');
                        document.getElementById('product')?.dispatchEvent(event);
                    }
                }, 100);
            });
        </script>
<?php 
// Clear session data after displaying - but only if we're showing them
if (!empty($errors)) {
    unset($_SESSION['form_data']);
    unset($_SESSION['form_errors']);
}
include 'footer.inc'; 
?>