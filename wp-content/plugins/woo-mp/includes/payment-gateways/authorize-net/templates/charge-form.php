<?php defined( 'ABSPATH' ) || die; ?>

<?php $this->template( 'card-fields' ); ?>

<?php $this->template( 'charge-amount-field' ); ?>

<div class="field-container">
    <button type="button" id="toggle-additional-details" class="button field" data-toggle="collapse" aria-controls="additional-details" aria-expanded="false">
        Additional Details - Level 2 Data
    </button>
</div>

<div id="additional-details" class="field-container" hidden>
    <div class="field-container third">
        <label for="tax-amount">Tax Amount</label>
        <input type="number" min="0" step="any" id="tax-amount" class="field" placeholder="0" />
    </div>
    <div class="field-container third">
        <label for="freight-amount">Freight Amount</label>
        <input type="number" min="0" step="any" id="freight-amount" class="field" placeholder="0" />
    </div>
    <div class="field-container third">
        <label for="duty-amount">Duty Amount</label>
        <input type="number" min="0" step="any" id="duty-amount" class="field" placeholder="0" />
    </div>
    <div class="field-container">
        <label for="po-number">PO Number</label>
        <input type="text" id="po-number" class="field" placeholder="Purchase Order Number" />
    </div>
    <div class="field-container">
        <input type="checkbox" id="tax-exempt" class="field tax-exempt-checkbox" />
        <label for="tax-exempt">Tax Exempt</label>
    </div>
</div>

<?php $this->template( 'charge-button' ); ?>