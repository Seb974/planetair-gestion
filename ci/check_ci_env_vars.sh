#!/usr/bin/env bash

if [[ -z "${PROJECT_ID}" ]]; then echo 'PROJECT_ID is not defined in your CI environement variables.'; fi
if [[ -z "${ADMIN_BUCKET}" ]]; then echo 'ADMIN_BUCKET is not defined in your CI environement variables.'; fi
if [[ -z "${CLIENT_BUCKET}" ]]; then echo 'CLIENT_BUCKET is not defined in your CI environement variables.'; fi
if [[ -z "${CI_SERVICE_ACCOUNT}" ]]; then echo 'CI_SERVICE_ACCOUNT is not defined in your CI repository environement variables.'; fi
if [[ -z "${DATABASE_USER}" ]]; then echo 'DATABASE_USER is not defined in your CI environement variables.'; fi
if [[ -z "${DATABASE_PASSWORD}" ]]; then echo 'DATABASE_PASSWORD is not defined in your CI environement variables.'; fi
if [[ -z "${DATABASE_NAME}" ]]; then echo 'DATABASE_NAME is not defined in your CI environement variables.'; fi
