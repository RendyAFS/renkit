@php
    $langCrud = collect(trans('crud'))->toArray();
    $langCustomAlert = [
        'success' => __('custom-alert.Success'),
        'info' => __('custom-alert.Information'),
        'warning' => __('custom-alert.Warning'),
        'error' => __('custom-alert.Error'),
        'ok' => __('custom-alert.OK'),
        'confirm' => __('custom-alert.Confirm'),
        'cancel' => __('custom-alert.Cancel'),
        'confirmation' => __('custom-alert.Confirmation'),
        'deleteConfirmation' => __('custom-alert.Delete Confirmation'),
        'delete' => __('custom-alert.Delete'),
        'deleteMessage' => __('custom-alert.Are you sure you want to delete this item? This action cannot be undone.'),
    ];
    $langApiProvider = [
        'networkError' => __('api-provider.Network Error or Server Down'),
        'invalidSession' => __('api-provider.Invalid Session'),
        'accessDenied' => __('api-provider.Access Denied'),
        'notFound' => __('api-provider.Data not found'),
        'pageExpired' => __('api-provider.Page Expired. Refreshing...'),
        'validationError' => __('api-provider.Validation Error'),
        'serverError' => __('api-provider.Server Error'),
        'internalServerError' => __('api-provider.Internal Server Error'),
        'unknownError' => __('api-provider.Unknown Error'),
    ];
@endphp

<script>
    window.langCustomAlert = @json($langCustomAlert);
    window.langApiProvider = @json($langApiProvider);
    window.langCrud = @json($langCrud);
</script>
