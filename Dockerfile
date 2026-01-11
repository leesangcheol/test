FROM php:8.2-apache

# 환경 변수 설정
ARG APP_ENV=development
ARG GIT_BRANCH=develop
ARG APP_VERSION=v1.0.0

ENV APP_ENV=${APP_ENV}
ENV GIT_BRANCH=${GIT_BRANCH}
ENV APP_VERSION=${APP_VERSION}

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 소스 코드 복사
COPY index.php /var/www/html/
COPY *.md /var/www/html/ 2>/dev/null || true

# Apache 설정
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# 포트 노출
EXPOSE 80

# Apache 실행
CMD ["apache2-foreground"]
