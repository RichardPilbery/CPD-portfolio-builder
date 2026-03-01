@component('mail::message')
# Account migrated


Congratulations, your account has been successfully migrated to the new CPD portfolio builder.


Your new login details are:

@component('mail::panel')
+ Username: {{ $username }}
+ Password: {{ $password }}
@endcomponent

I suggest you change this once you've successfully logged in. Head to https://cpd.com to get started.


Thanks,<br>
Richard
@endcomponent
