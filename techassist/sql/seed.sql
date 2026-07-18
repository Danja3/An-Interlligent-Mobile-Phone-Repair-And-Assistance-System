-- TechAssist — seed data (Northern Nigeria). Import AFTER schema.sql.
-- Demo passwords:  admin@techassist.ng = Admin@1234   |  all others = Demo@1234
SET NAMES utf8mb4;

-- =========================================================
-- USERS (bcrypt hashes generated with PHP password_hash)
-- =========================================================
INSERT INTO users (id, name, email, password_hash, role) VALUES
 (1,'TechAssist Admin','admin@techassist.ng','$2y$10$CarGD2XpjiqP6.tCfIOSDO6KL6131Vwl38tMC2PS/njZIfMDwL03G','admin'),
 (2,'Aisha Bello','aisha@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','customer'),
 (3,'Musa Ibrahim','musa@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','customer'),
 (4,'Kano Phone Clinic','demo.kano@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (5,'Kaduna GSM Hub','demo.kaduna@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (6,'Borno Mobile Care','demo.maiduguri@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (7,'Sokoto Fix Point','demo.sokoto@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (8,'Zaria Tech Repairs','demo.zaria@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (9,'Jos Plateau Phones','demo.jos@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician'),
 (10,'Gombe Mobile Masters','pending.gombe@techassist.ng','$2y$10$OLAC.wVTYYYfM1BaAAWECe9lH6f5uaSop1LrCd4Cm9JdmZYk37YQu','technician');

-- =========================================================
-- PHONE BRANDS (local market order)
-- =========================================================
INSERT INTO phone_brands (id, name, slug, sort_order) VALUES
 (1,'Tecno','tecno',1),(2,'Infinix','infinix',2),(3,'itel','itel',3),
 (4,'Samsung','samsung',4),(5,'Xiaomi / Redmi','xiaomi',5),(6,'Nokia','nokia',6),
 (7,'Gionee','gionee',7),(8,'Oppo','oppo',8),(9,'Apple (iPhone)','apple',9);

INSERT INTO phone_models (brand_id, name, slug, release_year) VALUES
 (1,'Tecno Spark 10','tecno-spark-10',2023),(1,'Tecno Spark 20','tecno-spark-20',2024),
 (1,'Tecno Camon 20','tecno-camon-20',2023),(1,'Tecno Pop 7','tecno-pop-7',2023),
 (1,'Tecno Phantom V','tecno-phantom-v',2023),
 (2,'Infinix Hot 30','infinix-hot-30',2023),(2,'Infinix Hot 40','infinix-hot-40',2024),
 (2,'Infinix Note 30','infinix-note-30',2023),(2,'Infinix Note 40','infinix-note-40',2024),
 (2,'Infinix Smart 8','infinix-smart-8',2023),
 (3,'itel A60','itel-a60',2023),(3,'itel A70','itel-a70',2024),(3,'itel P40','itel-p40',2023),
 (3,'itel S23','itel-s23',2023),(3,'itel Vision 3','itel-vision-3',2022),
 (4,'Samsung Galaxy A05','samsung-galaxy-a05',2023),(4,'Samsung Galaxy A15','samsung-galaxy-a15',2024),
 (4,'Samsung Galaxy A35','samsung-galaxy-a35',2024),(4,'Samsung Galaxy J7','samsung-galaxy-j7',2016),
 (4,'Samsung Galaxy S21','samsung-galaxy-s21',2021),
 (5,'Redmi 13C','redmi-13c',2023),(5,'Redmi Note 13','redmi-note-13',2024),
 (5,'Redmi A3','redmi-a3',2024),(5,'Xiaomi Poco C65','poco-c65',2023),
 (6,'Nokia 105','nokia-105',2023),(6,'Nokia C32','nokia-c32',2023),(6,'Nokia G21','nokia-g21',2022),
 (7,'Gionee Max','gionee-max',2022),(7,'Gionee S12','gionee-s12',2021),
 (8,'Oppo A18','oppo-a18',2023),(8,'Oppo A38','oppo-a38',2023),(8,'Oppo Reno 8','oppo-reno-8',2022),
 (9,'iPhone 11','iphone-11',2019),(9,'iPhone 12','iphone-12',2020),
 (9,'iPhone 13','iphone-13',2021),(9,'iPhone XR','iphone-xr',2018);

-- =========================================================
-- PROBLEM CATEGORIES + SYMPTOMS
-- =========================================================
INSERT INTO problem_categories (id, name, slug, icon, description, sort_order) VALUES
 (1,'Screen','screen','screen','Cracks, unresponsive touch, lines or black screen.',1),
 (2,'Battery','battery','battery','Drains fast, overheats, swollen, or shuts down randomly.',2),
 (3,'Charging','charging','charging','Will not charge, charges slowly, or a loose charging port.',3),
 (4,'Power & Boot','power','power','Will not turn on, stuck on logo, or keeps restarting.',4),
 (5,'Network & SIM','network','network','No network, SIM not detected, dropped calls, or no data.',5),
 (6,'Audio','audio','audio','No speaker sound, caller cannot hear you, or weak earpiece.',6),
 (7,'Camera','camera','camera','Camera will not open, blurry photos, or black screen.',7),
 (8,'Software','software','software','Hangs, apps crash, storage full, or forgotten password.',8),
 (9,'Water Damage','water-damage','water-damage','Phone fell in water or got wet.',9);

INSERT INTO symptoms (category_id, name, slug, description) VALUES
 (1,'Cracked / broken glass','screen-cracked','The glass is cracked or shattered.'),
 (1,'Touch not responding','screen-no-touch','Display lights up but touch does not work.'),
 (1,'Lines or discoloration','screen-lines','Coloured lines, patches, or flicker on the display.'),
 (1,'Black screen (phone still rings)','screen-black','No display but the phone vibrates or rings.'),
 (2,'Drains very fast','battery-drain','Battery runs down quickly even with light use.'),
 (2,'Overheating','battery-overheat','Phone gets very hot during use or charging.'),
 (2,'Swollen / bulging','battery-swollen','The back cover is lifting or the battery looks puffed.'),
 (2,'Random shutdowns','battery-shutdown','Phone switches off by itself with charge remaining.'),
 (3,'Will not charge at all','charging-none','Nothing happens when plugged in.'),
 (3,'Charges very slowly','charging-slow','Takes far too long to charge.'),
 (3,'Loose port / must hold cable','charging-loose','Only charges when the cable is held at an angle.'),
 (4,'Will not turn on','power-dead','Completely unresponsive, no display or sound.'),
 (4,'Stuck on logo / bootloop','power-bootloop','Restarts at the brand logo and never finishes booting.'),
 (4,'Restarts by itself','power-restart','Reboots randomly during normal use.'),
 (5,'No network / no service','network-no-service','Shows no signal or "no service".'),
 (5,'SIM not detected','network-no-sim','Phone says no SIM or does not read the card.'),
 (5,'Dropped calls / weak signal','network-weak','Calls cut off or signal is very weak.'),
 (5,'No mobile data / internet','network-no-data','Calls work but data will not connect.'),
 (6,'No sound from speaker','audio-no-speaker','No sound from music, ringtone, or speaker.'),
 (6,'Caller cannot hear me','audio-no-mic','Your voice is not heard on calls.'),
 (6,'Earpiece low / no sound on calls','audio-earpiece','You cannot hear the other person on a call.'),
 (7,'Camera will not open / crashes','camera-crash','Camera app freezes or closes immediately.'),
 (7,'Blurry photos','camera-blurry','Pictures are out of focus or hazy.'),
 (7,'Black screen in camera','camera-black','Camera opens to a black preview.'),
 (8,'Phone hangs / freezes','software-hang','Becomes slow or unresponsive.'),
 (8,'Apps keep crashing','software-crash','Apps close on their own repeatedly.'),
 (8,'Storage full','software-storage','Cannot save photos or install apps.'),
 (8,'Forgot password / locked','software-locked','Locked out of the phone or Google/Apple account.'),
 (9,'Fell in water / got wet','water-wet','Phone was exposed to water or liquid.'),
 (9,'Screen flickering after water','water-flicker','Display misbehaves after getting wet.');

-- =========================================================
-- DIAGNOSES (generic, NGN pricing) — symptom resolved by slug
-- =========================================================
INSERT INTO diagnoses (symptom_id, probable_cause, diy_solution, cost_min, cost_max, severity, required_skill, requires_technician) VALUES
((SELECT id FROM symptoms WHERE slug='screen-cracked'),'The outer glass and/or the touch digitizer is broken. If the picture still shows, sometimes only the glass needs replacing; black patches mean the whole display is gone.',NULL,8000,60000,'high','Screen Replacement',1),
((SELECT id FROM symptoms WHERE slug='screen-no-touch'),'The touch digitizer has failed, or a software glitch has frozen the screen.','Restart the phone (hold power 10+ seconds). Remove any screen protector and wipe the screen. If touch still fails, the digitizer needs replacing.',7000,45000,'medium','Screen Replacement',1),
((SELECT id FROM symptoms WHERE slug='screen-lines'),'The LCD/OLED panel is damaged, usually from a knock or pressure.',NULL,9000,60000,'high','Screen Replacement',1),
((SELECT id FROM symptoms WHERE slug='screen-black'),'The display panel or its connector has failed even though the phone still works.','Force-restart and confirm it rings or vibrates. If there is still no picture, the screen must be replaced.',8000,55000,'high','Screen Replacement',1),
((SELECT id FROM symptoms WHERE slug='battery-drain'),'The battery has aged and lost capacity, or background apps are draining power.','Lower brightness, turn off background apps, check Settings > Battery for heavy apps. If still poor, replace the battery.',4000,18000,'medium','Battery Replacement',1),
((SELECT id FROM symptoms WHERE slug='battery-overheat'),'A worn battery, heavy app/gaming use, or a faulty charger is causing heat.','Remove the case while charging, keep out of direct sun, use the original charger. If it stays hot, have the battery checked.',4000,18000,'medium','Battery Replacement',1),
((SELECT id FROM symptoms WHERE slug='battery-swollen'),'The battery is swelling — this is a safety hazard and must be replaced immediately.','Stop using and stop charging now. Do not press the bulge. Take it to a technician for safe replacement.',4000,18000,'high','Battery Replacement',1),
((SELECT id FROM symptoms WHERE slug='battery-shutdown'),'The battery cannot hold voltage under load, or the power-management chip is faulty.','Charge to full and test. If it still shuts down with charge remaining, the battery or power IC needs service.',4000,30000,'medium','Battery Replacement',1),
((SELECT id FROM symptoms WHERE slug='charging-none'),'A dirty or damaged charging port, a bad cable/adapter, or a dead battery.','Try a different good cable and adapter. Gently clean lint from the port with a wooden toothpick (phone off). If still dead, the port or battery needs service.',3000,20000,'medium','Charging Port Repair',1),
((SELECT id FROM symptoms WHERE slug='charging-slow'),'A worn cable/adapter or a dirty port is reducing charging speed.','Use the original charger and a good cable, and clean the port gently. Avoid using the phone while charging.',3000,14000,'low','Charging Port Repair',1),
((SELECT id FROM symptoms WHERE slug='charging-loose'),'The charging port is physically loose and needs resoldering or replacement.',NULL,4000,20000,'medium','Charging Port Repair',1),
((SELECT id FROM symptoms WHERE slug='power-dead'),'A flat/dead battery, a stuck power button, or a motherboard fault.','Charge for 30 minutes then force-restart (hold power, or power + volume down, 10-20 seconds). If nothing, a technician must open it.',5000,45000,'high','Motherboard Repair',1),
((SELECT id FROM symptoms WHERE slug='power-bootloop'),'The operating system is corrupted, often after a bad update or low storage.','Try booting to recovery mode and clearing cache. A full reflash usually fixes it (back up first if possible).',3000,18000,'medium','Software Repair',1),
((SELECT id FROM symptoms WHERE slug='power-restart'),'A software fault, a failing battery, or a power IC problem.','Update software, uninstall recent apps, and test in safe mode. If it persists, have the battery and board checked.',4000,30000,'medium','Software Repair',1),
((SELECT id FROM symptoms WHERE slug='network-no-service'),'A loose/faulty SIM, wrong network settings, or a damaged antenna.','Toggle airplane mode, restart, reseat the SIM, set network selection to Automatic. If still no service, the antenna may need repair.',4000,35000,'medium','Motherboard Repair',1),
((SELECT id FROM symptoms WHERE slug='network-no-sim'),'A dirty SIM, a faulty SIM tray, or a damaged SIM reader.','Power off, clean the SIM gold contacts, and try it in another slot or phone. If unread everywhere, the SIM reader needs service.',4000,28000,'medium','Motherboard Repair',1),
((SELECT id FROM symptoms WHERE slug='network-weak'),'A weak antenna connection or a software/carrier setting issue.','Update carrier/network settings and test in different locations. If only this phone is weak, the antenna may need repair.',4000,30000,'low','Motherboard Repair',1),
((SELECT id FROM symptoms WHERE slug='network-no-data'),'Incorrect APN (internet) settings or a disabled data setting.','Turn on Mobile Data, then reset the APN settings for your network (MTN, Airtel, Glo, 9mobile). Restart the phone. Usually a free fix.',0,5000,'low','Software Repair',0),
((SELECT id FROM symptoms WHERE slug='audio-no-speaker'),'A blown/blocked loudspeaker or a software/volume issue.','Make sure silent mode is off and volume is up. Restart and test with headphones. If silent or buzzing, the speaker needs replacing.',3000,16000,'medium','Speaker Replacement',1),
((SELECT id FROM symptoms WHERE slug='audio-no-mic'),'The microphone is blocked or faulty.','Remove any case and clean the small mic hole (bottom edge). Test with a voice recorder. If still unheard, the mic needs replacing.',3000,15000,'medium','Microphone Replacement',1),
((SELECT id FROM symptoms WHERE slug='audio-earpiece'),'The earpiece is clogged with dust or has failed.','Gently clean the earpiece mesh with a soft brush. Test on speaker mode — if that works, the earpiece needs cleaning or replacement.',3000,15000,'medium','Speaker Replacement',1),
((SELECT id FROM symptoms WHERE slug='camera-crash'),'A software glitch or a faulty camera module.','Restart, clear the Camera app cache (Settings > Apps > Camera), and update the system. If it still crashes, the module needs service.',5000,30000,'medium','Camera Replacement',1),
((SELECT id FROM symptoms WHERE slug='camera-blurry'),'A dirty lens, a cracked lens cover, or a failing autofocus.','Clean the rear lens with a soft cloth and remove any film. If still blurry, the camera module or lens cover may need replacing.',0,25000,'low','Camera Replacement',0),
((SELECT id FROM symptoms WHERE slug='camera-black'),'The camera module or its connector has failed.','Restart and clear the Camera app cache. If the preview stays black, the module needs replacing.',6000,30000,'medium','Camera Replacement',1),
((SELECT id FROM symptoms WHERE slug='software-hang'),'Full storage, too many background apps, or a software fault.','Free up storage, clear app caches, and restart. Update the system. Most of the time this is a free DIY fix.',0,12000,'low','Software Repair',0),
((SELECT id FROM symptoms WHERE slug='software-crash'),'A corrupted app or an out-of-date system.','Update or reinstall the affected app, clear its cache, and update the system. If many apps crash, a software repair may be needed.',0,10000,'low','Software Repair',0),
((SELECT id FROM symptoms WHERE slug='software-storage'),'The phone storage is full.','Delete unneeded photos/videos, move media to an SD card or cloud, and clear app caches. This is a free DIY fix.',0,0,'low',NULL,0),
((SELECT id FROM symptoms WHERE slug='software-locked'),'The phone is locked by a forgotten screen lock or a Google/Apple account.','Try your known passwords first. Account locks need proof of ownership — a technician can help with a legitimate unlock or factory reset.',3000,18000,'medium','Software Repair',1),
((SELECT id FROM symptoms WHERE slug='water-wet'),'Liquid has entered the phone and can cause corrosion within hours.','Power off immediately and DO NOT charge it. Dry the outside, remove SIM/SD, and take it to a technician fast. Avoid the rice myth — it does not stop internal corrosion.',5000,45000,'high','Water Damage Repair',1),
((SELECT id FROM symptoms WHERE slug='water-flicker'),'Liquid has reached the display or its connector.','Power off and do not charge. Get it cleaned professionally as soon as possible; the screen may also need replacing.',8000,55000,'high','Water Damage Repair',1);

-- =========================================================
-- TECHNICIAN PROFILES (6 approved + 1 pending) + skills
-- =========================================================
INSERT INTO technician_profiles (id,user_id,business_name,bio,shop_address,city,whatsapp_number,status,avg_rating,review_count) VALUES
 (1,4,'Kano Phone Clinic','8 years fixing Tecno, Infinix and itel phones. Same-day screen and battery work. Fair, transparent pricing.','Shop 12, Farm Centre GSM Market','Kano','2348030000001','approved',5.00,1),
 (2,5,'Kaduna GSM Hub','Board-level repair specialists. We handle water damage, charging ports and no-power faults.','Suite 4, Kachia Road Plaza','Kaduna','2348030000002','approved',4.00,1),
 (3,6,'Borno Mobile Care','Trusted neighbourhood repair shop. Screens, batteries, software flashing and unlocking.','Baga Road, near Monday Market','Maiduguri','2348030000003','approved',0,0),
 (4,7,'Sokoto Fix Point','Fast, friendly service for all Android brands. Original and quality parts available.','Sahara GSM Plaza, City Centre','Sokoto','2348030000004','approved',0,0),
 (5,8,'Zaria Tech Repairs','Student-friendly prices. Specialists in Samsung and Redmi screen and battery replacement.','Samaru, opposite ABU main gate','Zaria','2348030000005','approved',0,0),
 (6,9,'Jos Plateau Phones','Over a decade of experience. Camera, speaker, mic and motherboard repairs done right.','Terminus Market, Jos','Jos','2348030000006','approved',0,0),
 (7,10,'Gombe Mobile Masters','New shop offering screen, battery and software repairs at honest prices.','Pantami Ward, Gombe','Gombe','2348030000007','pending',0,0);

INSERT INTO technician_skills (technician_id, skill) VALUES
 (1,'Screen Replacement'),(1,'Battery Replacement'),(1,'Charging Port Repair'),
 (2,'Motherboard Repair'),(2,'Water Damage Repair'),(2,'Charging Port Repair'),
 (3,'Screen Replacement'),(3,'Software Repair'),(3,'Battery Replacement'),
 (4,'Screen Replacement'),(4,'Battery Replacement'),
 (5,'Screen Replacement'),(5,'Battery Replacement'),(5,'Software Repair'),
 (6,'Camera Replacement'),(6,'Speaker Replacement'),(6,'Microphone Replacement'),(6,'Motherboard Repair'),
 (7,'Screen Replacement'),(7,'Battery Replacement'),(7,'Software Repair');

-- =========================================================
-- BOOKINGS (every status) + REVIEWS
-- =========================================================
INSERT INTO bookings (id,customer_id,technician_id,symptom_label,model_label,customer_notes,technician_notes,status) VALUES
 (1,2,1,'Cracked screen','Tecno Spark 10','Dropped it on tiles, glass is shattered but display still shows.','Replaced screen, tested touch. All good.','completed'),
 (2,2,1,'Battery drains fast','Infinix Hot 30','Lasts only 3 hours now.',NULL,'requested'),
 (3,3,1,'Will not charge','Samsung Galaxy A15','Tried two cables, nothing.','Bring it in tomorrow morning, I will check the port.','accepted'),
 (4,3,1,'No sound from speaker','Redmi 13C','No ringtone or music sound.','Opened it, cleaning the speaker mesh now.','in_progress'),
 (5,2,2,'Fell in water','itel A70','Fell inside a bucket of water.','Cleaned the board, dried and reassembled. Working again.','completed'),
 (6,3,6,'Phone hangs / freezes','Nokia C32','Very slow lately.',NULL,'cancelled');

INSERT INTO reviews (booking_id, technician_id, customer_id, rating, comment) VALUES
 (1,1,2,5,'Fixed my cracked screen the same day. Very honest pricing — highly recommend.'),
 (5,2,2,4,'Saved my phone after it fell in water. Good, careful work.');
