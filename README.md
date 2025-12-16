# Laravel Facebook Clone

This project is a simplified Facebook-style social network built with a Laravel API backend and a Vue.js single-page application frontend. It mirrors the workflow from the Udemy course “[Facebook Clone with Laravel, TDD, Vue & Tailwind CSS](https://www.udemy.com/course/facebook-clone-with-laravel-tdd-vue-tailwind-css/)”.

## Core capabilities
- Authenticated users can view a news feed of their own and friends’ posts.
- Create text or image posts with server-side image processing.
- Like and comment on posts.
- Visit user profiles, including cover and profile photos.
- Send, accept, or ignore friend requests and manage friendship status.

## Architecture overview
- **Backend:** Laravel 6 API with Passport authentication and Eloquent models/resources for posts, comments, friendships, and users. Intervention Image handles image resizing and storage on the public disk.
- **Frontend:** Vue 2 SPA with Vue Router and Vuex modules for user, profile, and post state. Axios powers API calls, and Dropzone enables drag-and-drop uploads. Tailwind CSS provides styling utilities.

## Key workflows
- **Posting:** Users submit text and optionally upload an image. Images are processed and stored by the API, then returned for immediate display in the feed.
- **Reactions and comments:** Like/unlike toggles and new comments are sent through API endpoints and reflected in Vuex state to update counts inline.
- **Profiles and friendships:** Profile pages display user details and friendship state. Friend requests, acceptance, and ignoring are executed via dedicated endpoints and update the UI through store actions.

## Strengths and limitations
- **Strengths:**
  - Clear RESTful API design with JSON resources consumed by the SPA.
  - End-to-end media handling (Dropzone → API → stored image URLs) with immediate UI updates.
  - Vuex-managed state keeps feed, profile, and friendship data in sync across components.
- **Limitations:**
  - Validation and authorization are basic; stricter rules and policies would harden the API.
  - Routing and UI flows are minimal, leaving room for additional sections (e.g., notifications or messaging).

## Getting started
1. Install PHP and Node.js dependencies:
   ```bash
   composer install
   npm install
   ```
2. Copy `.env.example` to `.env`, set your database credentials, and generate an app key:
   ```bash
   php artisan key:generate
   ```
3. Run database migrations and seed any required data:
   ```bash
   php artisan migrate
   ```
4. Build frontend assets and start the development server:
   ```bash
   npm run dev
   php artisan serve
   ```

With the server running, visit the app in your browser to explore the news feed, create posts, and interact with friendships.
