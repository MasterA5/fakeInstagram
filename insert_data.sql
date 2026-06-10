-- ============================================
-- FakeSocial - Datos de prueba
-- Contraseña para todos los usuarios: 123456
-- ============================================

USE fakeInstagram;

-- ============================================
-- USERS
-- ============================================
INSERT INTO users (id, username, email, password, display_name, bio, avatar, theme) VALUES
    ('9ed8760b-740b-4768-8095-302b7f811980', 'maria_dev', 'maria@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Maria García', 'Desarrolladora web y amante del café ☕', 'https://api.dicebear.com/7.x/avataaars/svg?seed=maria_dev', 'dark'),
    ('55152357-1f1b-400a-b2d0-98ce74dfedee', 'carlos_art', 'carlos@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Carlos López', 'Artista digital • Diseñador UI', 'https://api.dicebear.com/7.x/avataaars/svg?seed=carlos_art', 'ocean'),
    ('aec9e7d6-250b-4418-bf5f-6138befeee15', 'laura_foto', 'laura@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Laura Martínez', 'Fotógrafa de naturaleza 🌿 Viajera', 'https://api.dicebear.com/7.x/avataaars/svg?seed=laura_foto', 'sunset'),
    ('7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'pedro_code', 'pedro@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Pedro Rodríguez', 'Full-stack developer • PHP & JS', 'https://api.dicebear.com/7.x/avataaars/svg?seed=pedro_code', 'dark'),
    ('0b38a3f5-74da-400d-86fd-a694c98de857', 'ana_music', 'ana@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Ana Sánchez', 'Música y tecnología 🎵', 'https://api.dicebear.com/7.x/avataaars/svg?seed=ana_music', 'light'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', 'admin', 'admin@example.com', '$2y$12$/jmXraiJEh.bs1L.aouv4.42WqFGFXfAb1elzy/01Hpe3cNtgERs2', 'Admin FakeSocial', 'Administrador de la plataforma', 'https://api.dicebear.com/7.x/avataaars/svg?seed=admin', 'dark');

-- ============================================
-- POSTS
-- ============================================
INSERT INTO posts (id, user_id, content, image, created_at) VALUES
    ('df39c4f0-b042-48d7-9893-d4719f55494c', '9ed8760b-740b-4768-8095-302b7f811980', 'Acabo de terminar mi nuevo proyecto con PHP y Tailwind. Increíble lo rápido que se puede prototipar.', 'https://picsum.photos/seed/p1/600/500', '2026-06-09 10:30:00'),
    ('48008b63-4a21-4f0d-9a02-3034e8904878', '55152357-1f1b-400a-b2d0-98ce74dfedee', 'Mi último diseño UI para una app de música. ¿Qué opinan?', 'https://picsum.photos/seed/p2/600/500', '2026-06-09 11:00:00'),
    ('d868fb33-3a96-4351-94b2-ff6c9dc9a0dc', 'aec9e7d6-250b-4418-bf5f-6138befeee15', 'Amanecer en la montaña. No hay nada como empezar el día así.', 'https://picsum.photos/seed/p3/600/500', '2026-06-09 12:00:00'),
    ('49b4f3ab-4d2d-4fa9-af61-d8c4442b2146', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'Nuevo récord personal: 1000 líneas de código en un día. El café ayudó.', 'https://picsum.photos/seed/p4/600/500', '2026-06-09 14:00:00'),
    ('370c54e3-fdf2-4a78-8173-2afe578d0484', '0b38a3f5-74da-400d-86fd-a694c98de857', 'Tocando en vivo esta noche en el Teatro Principal. Últimos tickets disponibles 🎫', 'https://picsum.photos/seed/p5/600/500', '2026-06-09 16:00:00'),
    ('1404afee-ed52-49af-9bdf-9330207330c9', '9ed8760b-740b-4768-8095-302b7f811980', 'Tip del día: siempre sanitizen sus inputs. El SQL injection sigue siendo el #1 de OWASP por algo.', NULL, '2026-06-08 09:00:00'),
    ('c9b998c7-849c-4f2a-9a7d-86811bd2c36f', '55152357-1f1b-400a-b2d0-98ce74dfedee', 'Nueva identidad visual para una marca local. Me encanta el resultado.', 'https://picsum.photos/seed/p7/600/500', '2026-06-08 15:00:00'),
    ('a6ff43b1-8fec-4e2f-9cb7-dc284c02fb33', 'aec9e7d6-250b-4418-bf5f-6138befeee15', 'Atardecer desde el cerro. La naturaleza siempre gana.', 'https://picsum.photos/seed/p8/600/500', '2026-06-07 18:30:00'),
    ('da66b4f2-a719-4dc8-b422-eb48c174b49d', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'Refactorizando código legacy. Es como una cirugía pero con más bugs.', NULL, '2026-06-07 10:00:00'),
    ('a807444a-7f10-45a3-8774-612e8cea3b5b', '0b38a3f5-74da-400d-86fd-a694c98de857', 'Nueva canción subida a mi canal. Link en bio!', 'https://picsum.photos/seed/p10/600/500', '2026-06-06 20:00:00');

-- ============================================
-- COMMENTS
-- ============================================
INSERT INTO comments (id, post_id, user_id, content, created_at) VALUES
    ('2b1603e5-31c8-4171-8724-45f65e51c150', 'df39c4f0-b042-48d7-9893-d4719f55494c', '55152357-1f1b-400a-b2d0-98ce74dfedee', 'Se ve genial! Compartís el repo?', '2026-06-09 11:00:00'),
    ('856b2af7-ce1a-4dda-a307-0a3249d42958', 'df39c4f0-b042-48d7-9893-d4719f55494c', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'Buen trabajo Maria!', '2026-06-09 11:30:00'),
    ('9e042269-48f3-4f29-a98d-fdd3a52c8fba', '48008b63-4a21-4f0d-9a02-3034e8904878', '9ed8760b-740b-4768-8095-302b7f811980', 'Me encanta el gradiente que usaste', '2026-06-09 11:30:00'),
    ('91c30c8c-87c6-4442-808e-0d60b832b00e', '48008b63-4a21-4f0d-9a02-3034e8904878', '0b38a3f5-74da-400d-86fd-a694c98de857', 'Los colores son perfectos para una app de música', '2026-06-09 12:00:00'),
    ('177c43be-bf37-4b2b-9378-3d5ef4e32611', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '9ed8760b-740b-4768-8095-302b7f811980', 'Qué lugar es ese?', '2026-06-09 12:30:00'),
    ('293662bb-e12f-4772-9a45-9e124b6f1bf6', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'Espectacular foto!', '2026-06-09 13:00:00'),
    ('cc581298-b3aa-4317-96a7-903c3d151d29', '49b4f3ab-4d2d-4fa9-af61-d8c4442b2146', '9ed8760b-740b-4768-8095-302b7f811980', 'Jaja el café siempre es la respuesta', '2026-06-09 14:30:00'),
    ('631d8e13-1eb5-4a5d-ad00-049d1357ff80', '370c54e3-fdf2-4a78-8173-2afe578d0484', '55152357-1f1b-400a-b2d0-98ce74dfedee', 'Ojalá pudiera ir! Mucho éxito', '2026-06-09 16:30:00'),
    ('6076b795-f8a6-434f-82e2-5d5e0905a3a8', '1404afee-ed52-49af-9bdf-9330207330c9', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6', 'Totalmente de acuerdo, prepared statements siempre', '2026-06-08 10:00:00'),
    ('0949a44f-4899-44c6-8d44-c26abecf4da7', 'c9b998c7-849c-4f2a-9a7d-86811bd2c36f', 'aec9e7d6-250b-4418-bf5f-6138befeee15', 'Muy elegante el diseño', '2026-06-08 16:00:00'),
    ('eb92860b-f04e-408c-ba4f-8fd399a86171', 'a6ff43b1-8fec-4e2f-9cb7-dc284c02fb33', '0b38a3f5-74da-400d-86fd-a694c98de857', 'Qué colores! 😍', '2026-06-07 19:00:00'),
    ('3de74201-2868-42fd-83da-f5acefb86147', 'a807444a-7f10-45a3-8774-612e8cea3b5b', '9ed8760b-740b-4768-8095-302b7f811980', 'Ya la escuché, muy buena!', '2026-06-07 21:00:00');

-- ============================================
-- LIKES (each pair has a unique UUID id)
-- ============================================
INSERT INTO likes (id, post_id, user_id) VALUES
    ('516c2504-0049-4fb7-a7c9-2811069a0348', 'df39c4f0-b042-48d7-9893-d4719f55494c', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('c42667e9-3b76-46c8-8b2d-c9ff57cf5dfc', 'df39c4f0-b042-48d7-9893-d4719f55494c', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6'),
    ('2231d828-6c6a-4256-82ab-5db770122b4f', 'df39c4f0-b042-48d7-9893-d4719f55494c', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('20fb2537-1bd7-4cf4-9b02-43496f8ba004', '48008b63-4a21-4f0d-9a02-3034e8904878', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('45e6989c-55bd-4f52-9f33-3c54f3d5b351', '48008b63-4a21-4f0d-9a02-3034e8904878', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('05d5ffc1-a4dc-4a56-a7af-d157d475ec5f', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('b15a1aa0-3a55-4bfe-b85b-e2338550a2f0', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('dca55faf-9013-4773-91e3-c0f534ac7e52', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6'),
    ('1870c90e-7734-4364-b39f-70e952948b7e', 'd868fb33-3a96-4351-94b2-ff6c9dc9a0dc', '46044852-2f69-4cfa-8a87-77309f004bb0'),
    ('a8a5571a-46cf-4309-81dd-eef36d274eb5', '49b4f3ab-4d2d-4fa9-af61-d8c4442b2146', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('9dd8e7ec-964d-4c85-b64f-ade50d80f719', '49b4f3ab-4d2d-4fa9-af61-d8c4442b2146', 'aec9e7d6-250b-4418-bf5f-6138befeee15'),
    ('a0ccb28c-fd34-4a44-a035-32b0630bcd4a', '370c54e3-fdf2-4a78-8173-2afe578d0484', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('e7f24b42-4ec6-40c4-8cc7-d4526225910f', '370c54e3-fdf2-4a78-8173-2afe578d0484', 'aec9e7d6-250b-4418-bf5f-6138befeee15'),
    ('65c940b9-04ba-4ba9-9df8-3a1d4a35a173', '1404afee-ed52-49af-9bdf-9330207330c9', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('aec6f30e-c3ba-4a13-8faa-695a166c671b', '1404afee-ed52-49af-9bdf-9330207330c9', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6'),
    ('48b7ea85-1d60-4463-aa4a-d6af2aec8bf0', 'c9b998c7-849c-4f2a-9a7d-86811bd2c36f', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('be012180-e031-4ce5-a40a-a3ecdb4db45a', 'a6ff43b1-8fec-4e2f-9cb7-dc284c02fb33', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('1064966f-05a9-4ffa-9356-d26bdb72aea6', 'a6ff43b1-8fec-4e2f-9cb7-dc284c02fb33', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('dbd5c6e2-5d2e-49bc-adb8-c0cc3500de0d', 'a807444a-7f10-45a3-8774-612e8cea3b5b', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('b7c715ac-779b-46e6-bca9-598e6fecfc2f', 'a807444a-7f10-45a3-8774-612e8cea3b5b', 'aec9e7d6-250b-4418-bf5f-6138befeee15');

-- ============================================
-- FOLLOWS
-- ============================================
INSERT INTO follows (follower_id, followed_id) VALUES
    ('9ed8760b-740b-4768-8095-302b7f811980', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('9ed8760b-740b-4768-8095-302b7f811980', 'aec9e7d6-250b-4418-bf5f-6138befeee15'),
    ('9ed8760b-740b-4768-8095-302b7f811980', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6'),
    ('55152357-1f1b-400a-b2d0-98ce74dfedee', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('55152357-1f1b-400a-b2d0-98ce74dfedee', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('aec9e7d6-250b-4418-bf5f-6138befeee15', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('aec9e7d6-250b-4418-bf5f-6138befeee15', '0b38a3f5-74da-400d-86fd-a694c98de857'),
    ('7b6c4cc5-207c-4b91-838f-daaefd48a2f6', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('7b6c4cc5-207c-4b91-838f-daaefd48a2f6', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('0b38a3f5-74da-400d-86fd-a694c98de857', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('0b38a3f5-74da-400d-86fd-a694c98de857', 'aec9e7d6-250b-4418-bf5f-6138befeee15'),
    ('0b38a3f5-74da-400d-86fd-a694c98de857', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', '9ed8760b-740b-4768-8095-302b7f811980'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', '55152357-1f1b-400a-b2d0-98ce74dfedee'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', 'aec9e7d6-250b-4418-bf5f-6138befeee15'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', '7b6c4cc5-207c-4b91-838f-daaefd48a2f6'),
    ('46044852-2f69-4cfa-8a87-77309f004bb0', '0b38a3f5-74da-400d-86fd-a694c98de857');
