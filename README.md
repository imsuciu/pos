# Auto Service Work Management App (PHP + MySQL)

Aplicatie web pentru management lucrari service auto, cu roluri Admin si Tehnician.

## Cerinte
- PHP 8.1+
- MySQL 8+ / MariaDB 10.5+
- Apache/Nginx (LAMP, cPanel, Cloudron)

## Instalare
1. Creeaza baza de date, ex: `auto_service`.
2. Ruleaza scriptul SQL:
   ```bash
   mysql -u root -p auto_service < database/schema.sql
   ```
3. Copiaza `config/config.example.php` in `config/config.php` si completeaza datele de conexiune.
4. Configureaza web root pe folderul `public`.
5. Acceseaza aplicatia si autentifica-te cu conturile demo.

## Conturi demo
- Admin: `admin` / `Admin123!`
- Tehnician 1: `tech1` / `Tech123!`
- Tehnician 2: `tech2` / `Tech123!`
- Tehnician 3: `tech3` / `Tech123!`

## Structura
- `public/` - punct de intrare si pagini
- `src/` - logica backend (DB, auth, services)
- `database/schema.sql` - migrari + date demo
- `config/` - configurare baza de date si timezone

## Functionalitati acoperite
- Login cu roluri Admin/Technician
- Dashboard Admin cu KPI
- CRUD Work Orders, Operation Templates, Clients, Vehicles, Workstations
- Atribuire tehnicieni si operatiuni multiple pe lucrare
- Timer server-side cu sesiuni start/pauza/reluare/finalizare
- Task board Tehnician cu checklist operatiuni si observatii
- Audit log pentru schimbari importante
- Rapoarte cu filtre + export CSV

## Timezone
Aplicatia foloseste `Europe/Bucharest`.
