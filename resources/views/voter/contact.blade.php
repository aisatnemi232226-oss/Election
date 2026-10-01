@extends('layouts.voter')

@section('title', 'Contact Us')

@section('content')
<div class="page-content">
    <header class="page-intro">
        <h1>Contact us</h1>
        <p>We would love to hear from you! Please use the contact information below to get in touch with us.</p>
    </header>

    <section class="surface-card contact-location">
        <h2>Our location</h2>
        <iframe
            title="Map showing the university location"
            loading="lazy"
            allowfullscreen
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.086509486048!2d144.9630579153167!3d-37.81410797975195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d1f9f1b1a1b1!2sUniversity%20of%20Melbourne!5e0!3m2!1sen!2sau!4v1633071234567!5m2!1sen!2sau"
        ></iframe>
    </section>

    <section class="surface-card">
        <h2>Contact information</h2>
        <div class="surface-grid two-column">
            <article class="surface-subcard">
                <h3>Address</h3>
                <p>123 University Ave, City, Country</p>
            </article>
            <article class="surface-subcard">
                <h3>Phone</h3>
                <p>+123 456 7890</p>
            </article>
            <article class="surface-subcard">
                <h3>Email</h3>
                <p><a class="text-link" href="mailto:contact@university.edu">contact@university.edu</a></p>
            </article>
            <article class="surface-subcard">
                <h3>Office hours</h3>
                <p>Mon - Fri: 9:00 AM - 5:00 PM</p>
            </article>
        </div>
    </section>
</div>
@endsection
