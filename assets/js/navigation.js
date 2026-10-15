/**
 * Accessible mobile menu toggle for TheOct.
 */
( function () {
    'use strict';

    var toggle = document.querySelector( '.menu-toggle' );
    var navigation = document.querySelector( '.primary-navigation' );

    if ( ! toggle || ! navigation ) {
        return;
    }

    toggle.addEventListener( 'click', function () {
        var isOpen = navigation.classList.toggle( 'is-open' );
        toggle.setAttribute( 'aria-expanded', String( isOpen ) );
    } );

    navigation.addEventListener( 'keydown', function ( event ) {
        if ( event.key === 'Escape' ) {
            navigation.classList.remove( 'is-open' );
            toggle.setAttribute( 'aria-expanded', 'false' );
            toggle.focus();
        }
    } );

    window.addEventListener( 'resize', function () {
        if ( window.innerWidth > 760 ) {
            navigation.classList.remove( 'is-open' );
            toggle.setAttribute( 'aria-expanded', 'false' );
        }
    } );
} )();
