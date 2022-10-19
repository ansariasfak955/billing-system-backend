<div class="row">
    <div class="col-lg-6">
        <div class="mb-3">
            <div class="form-group"><label for="smtp_email_address" class="">Email address</label><input name="smtp_email_address" id="smtp_email_address" type="email" class="is-touched is-pristine av-valid form-control" value="{{getSettingValue('smtp_email_address')}}" /></div>
        </div>
        <div class="mb-3">
            <div class="form-group"><label for="smtp_server" class="">SMTP Server</label><input name="smtp_server" id="smtp_server" type="text" class="is-touched is-pristine av-valid form-control" value="{{getSettingValue('smtp_server')}}" /></div>
        </div>
        <div class="mb-3">
            <div class="form-group">
                <label for="smtp_security_protocol" class="">Security Protocol</label>
                <select name="smtp_security_protocol" id="smtp_security_protocol" class="is-touched is-pristine av-valid form-control">
                    <option value="">None</option>
                    <option value="STARTTLS" @if(getSettingValue('smtp_security_protocol') == 'STARTTLS') selected @endif >STARTTLS</option>
                    <option value="TLS" @if(getSettingValue('smtp_security_protocol') == 'TLS') selected @endif >TLS</option>
                    <option value="SSL" @if(getSettingValue('smtp_security_protocol') == 'SSL') selected @endif >SSL</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mb-3">
            <div class="form-group"><label for="smtp_password" class="">Email password</label><input name="smtp_password" id="smtp_password" type="password" class="is-untouched is-pristine av-valid form-control" value="{{getSettingValue('smtp_password')}}" /></div>
        </div>
        <div class="mb-3">
            <div class="form-group"><label for="smtp_port" class="">SMTP Port</label><input name="smtp_port" id="smtp_port" type="number" class="is-touched is-pristine av-valid form-control" value="{{getSettingValue('smtp_port')}}" /></div>
        </div>
    </div>
</div>
