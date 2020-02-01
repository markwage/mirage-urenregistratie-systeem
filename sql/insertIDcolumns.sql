/* Insert kolom IDuser in tabel 'approvals' */
ALTER TABLE approvals
ADD column IDusers int(11);

/* Insert kolom IDusers in tabel 'uren' */
ALTER TABLE uren
ADD column IDusers int(11);

/* Insert kolom IDuser in tabel 'uren' */
ALTER TABLE uren
ADD column IDsoorturen int(11);

/* Insert kolom IDuser in tabel 'uren' */
ALTER TABLE uren
ADD column IDapprovedbyuser int(11);

/* Creeer de views opnieuw met extra kolom */
