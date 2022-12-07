@component($typeForm, get_defined_vars())
    <button type="button" id="ckeditor-opener-handler" {{ $attributes }} disabled>

        @isset($icon)
            <x-orchid-icon :path="$icon" class="{{ empty($name) ?: 'me-2'}}"/>
        @endisset

        {{ $name ?? '' }}
    </button>
@endcomponent

@push('scripts')
    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', (event) => {
            let integrationButton = document.querySelector('button#ckeditor-opener-handler');

            if (!integrationButton) {
                throw new Error('Integration Button Error');
            }

            let radioInputChecked = document.querySelector('input.form-check-input[type=radio]:checked');

            let radioInputList = document.querySelectorAll('input.form-check-input[type=radio]');

            if (!radioInputList) {
                let toastWrapper = document.querySelector('[data-controller="toast"]');
                let toastController = application.getControllerForElementAndIdentifier(toastWrapper, 'toast');
                toastController.toast('You did not select a file');

                return;
            }

            radioInputList?.forEach(value => {
                value.addEventListener('click', (event) => {
                    if (integrationButton.hasAttribute('disabled')) {
                        integrationButton.removeAttribute('disabled');
                    }
                })
            });

            if (radioInputChecked) {
                integrationButton.removeAttribute('disabled');
            }

            integrationButton.addEventListener('click', (event) => {
                integrationButton.setAttribute('disabled', 'disabled');

                let checkedInput = document.querySelector('input.form-check-input[type=radio]:checked');

                let fileUrl = checkedInput.getAttribute('data-url');

                let funcNum = integrationButton.getAttribute('data-ckeditor-func-num')

                window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);

                window.close();
            });
        })
    </script>
@endpush
