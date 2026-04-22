
{{-- BUSINESS --}}
<fieldset style="margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px;">
    <legend><strong>Business Details</strong></legend>

    <label>Business Name *</label><br>
    <input type="text" name="business_name" style="width:300px; padding:5px;"
           value="{{ old('business_name', $vendor->business_name ?? '') }}" required><br><br>

    <label>Business Type *</label><br>
    <select name="business_type" style="width:312px; padding:5px;" required>
        <option value="">-- Select Type --</option>
        @foreach(['Individual','Proprietorship','Partnership','Pvt Ltd','LLP'] as $type)
            <option value="{{ $type }}"
                {{ old('business_type', $vendor->business_type ?? '') == $type ? 'selected' : '' }}>
                {{ $type }}
            </option>
        @endforeach
    </select><br><br>
</fieldset>

{{-- CONTACT --}}
<fieldset style="margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px;">
    <legend><strong>Contact Details</strong></legend>

    <label>Contact Person Name *</label><br>
    <input type="text" name="contact_person_name" style="width:300px; padding:5px;"
           value="{{ old('contact_person_name', $vendor->contact_person_name ?? '') }}" required><br><br>

    <label>Contact Email *</label><br>
    <input type="email" name="contact_email" style="width:300px; padding:5px;"
           value="{{ old('contact_email', $vendor->contact_email ?? '') }}" required><br><br>

    <label>Contact Mobile * (10-digit, starting with 6–9)</label><br>
    <input type="text" name="contact_mobile" maxlength="10" style="width:300px; padding:5px;"
           value="{{ old('contact_mobile', $vendor->contact_mobile ?? '') }}" required><br><br>
</fieldset>

{{-- IDENTITY --}}
<fieldset style="margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px;">
    <legend><strong>Identity</strong></legend>

    <label>Company PAN * (e.g. ABCDE1234F)</label><br>
    <input type="text" name="pan_number" maxlength="10"
           style="width:300px; padding:5px; text-transform:uppercase;"
           value="{{ old('pan_number', $vendor->pan_number ?? '') }}" required><br><br>

    <label>GST Number (optional, e.g. 27ABCDE1234F1Z5)</label><br>
    <input type="text" name="gst_number" maxlength="15"
           style="width:300px; padding:5px; text-transform:uppercase;"
           value="{{ old('gst_number', $vendor->gst_number ?? '') }}"><br><br>
</fieldset>

{{-- ADDRESS --}}
<fieldset style="margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px;">
    <legend><strong>Address</strong></legend>

    <label>Address *</label><br>
    <textarea name="address" style="width:300px; padding:5px; height:60px;" required>{{ old('address', $vendor->address ?? '') }}</textarea><br><br>

    <label>City *</label><br>
    <input type="text" name="city" style="width:300px; padding:5px;"
           value="{{ old('city', $vendor->city ?? '') }}" required><br><br>

    <label>State *</label><br>
    <input type="text" name="state" style="width:300px; padding:5px;"
           value="{{ old('state', $vendor->state ?? '') }}" required><br><br>

    <label>Pincode * (6-digit)</label><br>
    <input type="text" name="pincode" maxlength="6" style="width:300px; padding:5px;"
           value="{{ old('pincode', $vendor->pincode ?? '') }}" required><br><br>
</fieldset>

{{-- BANK --}}
<fieldset style="margin-bottom:16px; padding:12px; border:1px solid #ddd; border-radius:4px;">
    <legend><strong>Bank Details</strong></legend>

    <label>Account Holder Name *</label><br>
    <input type="text" name="account_holder_name" style="width:300px; padding:5px;"
           value="{{ old('account_holder_name', $vendor->account_holder_name ?? '') }}" required><br><br>

    <label>Account Number *
        @isset($vendor)
            <small style="color:#888;">(re-enter to update)</small>
        @endisset
    </label><br>
    {{-- Never pre-fill — user must re-enter for security --}}
    <input type="text" name="account_number" style="width:300px; padding:5px;"
           minlength="9" maxlength="18"
           placeholder="{{ isset($vendor) ? 'Re-enter account number' : 'Enter account number' }}"
           required><br><br>

    <label>IFSC Code * (e.g. SBIN0001234)</label><br>
    <input type="text" name="ifsc_code" maxlength="11"
           style="width:300px; padding:5px; text-transform:uppercase;"
           value="{{ old('ifsc_code', $vendor->ifsc_code ?? '') }}" required><br><br>
</fieldset>
