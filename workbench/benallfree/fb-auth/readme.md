# Facebook Auth for Laravel

This package adds Facebook authentication to Laravel. It integrates with the standard `Auth` class and will authenticate using a Facebook `accessToken`.

# Installation

    composer require benallfree/fb-auth 1.0.*

# Setup

    php artisa migrate:publish benallfree/fb-auth

Update `app/config/app.php`:

    'Auth'              => 'BenAllfree\FbAuth\Auth',

# Usage

To use this package, you must use the Facebook JavaScript SDK. That will allow the user to authenticate client-side, which will yield a Facebook `authToken`. That token can be used to make server-side Facebook API calls.


