USE registro;

-- creates a new course populating info_corsi and lezioni
DELIMITER // 
CREATE PROCEDURE insert_corso(
    IN title_id INT,
    IN start_date DATE,
    IN end_date DATE,
    IN am BOOL,
    IN pm BOOL
)
BEGIN
    DECLARE curr_id INT;
    DECLARE curr_date DATE;
    DECLARE total_hours INT;
    
    INSERT INTO info_corsi (id_titolo, data_inizio, data_fine)
	VALUES (title_id, start_date, end_date);
   
    -- sets id and current date to compile 'lezioni'
    SET curr_id = (SELECT LAST_INSERT_ID());
    SET curr_date = start_date;
	
    -- checks if current date is a working day
    WHILE curr_date <= end_date DO
		IF NOT EXISTS (SELECT 1 FROM giorni_festivi WHERE data_festiva = curr_date) 
	        AND DAYOFWEEK(curr_date) NOT IN (1, 7) 
	            THEN
				-- populates 'lezioni' by morning and/or afternoon time slot
				IF am THEN
					INSERT INTO lezioni (id_corso, giorno, ora_inizio, ora_fine)
						VALUES (curr_id, curr_date, '09:00:00', '13:00:00');
				END IF;
	            IF pm THEN
					INSERT INTO lezioni (id_corso, giorno, ora_inizio, ora_fine)
						VALUES (curr_id, curr_date, '14:00:00', '18:00:00');
				END IF;
		END IF;
		-- increments date to continue the cycle
		SET curr_date = DATE_ADD(curr_date, INTERVAL 1 DAY);
	END WHILE;
    
    -- calculates the total hours of the lessons
	SET total_hours = (
		SELECT SUM(TIMESTAMPDIFF(HOUR, ora_inizio, ora_fine))
        FROM lezioni
        WHERE id_corso = curr_id
	);

	-- updates 'info_corsi' with the calculated total hours
	UPDATE info_corsi
	  SET totale_ore = total_hours
      WHERE id = curr_id;
END 
// DELIMITER ;


-- associates the students with a course based on the course id
DELIMITER //
CREATE PROCEDURE insert_corsi_per_corsista(
    IN course_id INT,
    IN set_student_id VARCHAR(100)
)
BEGIN
    DECLARE student_id INT;
    
    students_loop: LOOP
		-- breaks the loop if the set is empty
		IF set_student_id = '' THEN
			LEAVE students_loop; 
		END IF;
		
        -- extrapolates the first student in the list, separated by ','
        SET student_id = TRIM(SUBSTRING_INDEX(set_student_id, ',', 1));
        
        INSERT INTO corsi_per_corsista (id_corso, id_corsista)
        VALUES (course_id, student_id);

		-- deletes the last inserted student from list to continue the loop on the next one
        SET set_student_id = TRIM(BOTH ',' FROM SUBSTRING(set_student_id, LENGTH(student_id) + 1));
    END LOOP;
END 
// DELIMITER ;


-- associates one or more teachers with a course id
DELIMITER // 
CREATE PROCEDURE insert_corsi_per_docente(
    IN course_id INT,
    IN set_teacher_id VARCHAR(100)
)
BEGIN
    DECLARE teacher_id INT;
    
    teachers_loop: LOOP
		IF set_teacher_id = '' THEN
			LEAVE teachers_loop; 
		END IF;
    
        SET teacher_id = TRIM(SUBSTRING_INDEX(set_teacher_id, ',', 1));

        INSERT INTO corsi_per_docente (id_corso, id_docente)
        VALUES (course_id, teacher_id);

        SET set_teacher_id = TRIM(BOTH ',' FROM SUBSTRING(set_teacher_id, LENGTH(teacher_id) + 1));
    END LOOP;
END 
// DELIMITER ;


-- associates a range of lessons of a course with a teacher
DELIMITER //  
CREATE PROCEDURE insert_lezioni_per_docente(
  IN course_id INT,
  IN teacher_id INT,
  IN start_date DATE,
  IN end_date DATE, 
  IN am BOOL,
  IN pm BOOL
)
BEGIN
    DECLARE lesson_id INT;
	
    INSERT INTO lezioni_per_docente (id_docente, id_lezione)
    SELECT
        teacher_id,  -- id on entry
        id  -- id from table
    FROM
        lezioni
    WHERE
        lezioni.id_corso = course_id
        -- checks if lesson is included between the dates entered
        AND lezioni.giorno BETWEEN start_date AND end_date
        AND (
			-- checks the time slot of the lesson
            (am AND lezioni.ora_inizio >= '09:00:00' AND lezioni.ora_fine <= '13:00:00') OR
            (pm AND lezioni.ora_inizio >= '14:00:00' AND lezioni.ora_fine <= '18:00:00')
        );
END
// DELIMITER ;

