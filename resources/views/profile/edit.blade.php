<x-custom-admin-layout>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                @include('profile.partials.update-profile-information-form')
            </div>
            
            <div class="col-md-12">
                @include('profile.partials.update-password-form')
            </div>
            
            <div class="col-md-12 mt-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-custom-admin-layout>