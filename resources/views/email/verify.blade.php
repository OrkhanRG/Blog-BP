<h1>Doğrulama Maili</h1>

<p>
    Hər vaxtınız xeyirli olsun {{ $user->name }}
</p>

<p>
    Zəhmət olmasa aşağıdakı linkə giriş edərək mailinizi doğrulayınız.
</p> <br>

<a href="{{ route('verify-token', ['token' => $token]) }}">
    Maili Doğrula
</a>
