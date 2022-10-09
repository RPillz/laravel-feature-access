# Changelog

All notable changes to `laravel-feature-access` will be documented in this file.

## 1.1.1 - 2022-10-8

- New function: $user->hasAnyFeatures() to check if any custom features are set, or if they are using the default plan.
- Bug fix: checking limits can return 0 (zero)

## 1.1.0 - 2022-10-6

- New Feature: Get plan/level name from existing subscription. (from Cashier, Spark, etc.)
- New Functions: getFeatureLimit() and withinFeatureLimit() to better work with feature quotas.
- Bugfix: Throwing error when trying to lookup feature with non-existant level.
- Updated for Laravel 9

## 1.0.0 - 2021-11-20

- initial release
