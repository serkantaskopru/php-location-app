# Proje Kurulum Yönergeleri

Bu belge, projeyi kurmak ve çalıştırmak için adım adım yönergeleri içermektedir. Aşağıdaki adımları takip ederek uygulamanızı başlatabilirsiniz.

## Ön Koşullar

- **Docker** ve **Docker Compose** yüklü olmalıdır.
- Bu projeyi bilgisayarınıza klonlayın ve docker ile projeyi compose edin.

## 1. `.env` Dosyasını Kopyalayın

Proje dizininde bir `.env` dosyasına ihtiyacınız olacak. Eğer bu dosya yoksa, aşağıdaki komut ile örnek `.env` dosyasını oluşturabilirsiniz.

```bash
cp .env.example .env
```


## 2. Veritabanı bilgilerini `.env` dosyanıza aktarın

Proje dizininde `docker-compose.yml` dosyası içerisinde mysql alanında gerekli veritabanı bilgileri mevcut. Bu bilgileri `.env` dosyanıza aktarın. Hazır olarak aktarmak isterseniz bilgiler aşağıdaki gibi olmalıdır.

```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=locationapp
DB_USERNAME=root
DB_PASSWORD=password
```
## 3. Uygulama anahtarı oluşturun ve tabloları aktarın

Proje dizininde komut penceresini açın ve aşağıdaki komutları uygulayın.

```bash
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan key:generate --env=testing
docker-compose exec php php artisan migrate
docker-compose exec php php artisan migrate --env=testing
```

## 4. Uygulama uç noktalarını test edin

Proje dizininde komut penceresini açın ve aşağıdaki komutları sırayla uygulayın.
`php-location-app-php-1` container adı sizin containerinizde farklı olabilir bu alanı kendi container adınıza göre değiştirin.

```bash
docker exec -it php-location-app-php-1 bash
cd /var/www/html
php artisan test
```

## 5. Uygulamayı çalıştırın

Tüm adımlar sorunsuz uygulandıysa artık uygulamayı `http://localhost:8012` adresinden görüntüleyebilirsiniz.