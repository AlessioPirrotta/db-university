<?php

//1. Selezionare tutti gli studenti nati nel 1990 (160)
SELECT *
FROM `students` 
WHERE YEAR(date_of_birth) = 1990;

//2. Selezionare tutti i corsi che valgono più di 10 crediti (479)
SELECT * 
FROM `courses` 
WHERE cfu > 10;

//3. Selezionare tutti gli studenti che hanno più di 30 anni

SELECT * 
FROM `students` 
// where TIMESTAMPDIFF (YEAR, 'date of birth', CURDATE() )
WHERE YEAR(date_of_birth) < 1993
ORDER BY date_of_birth DESC;

//4.  Selezionare tutti i corsi del primo semestre del primo anno di un qualsiasi corso di laurea (286)

SELECT * 
FROM `courses` 
WHERE period = 'I semestre'AND year = 1;

//5. Selezionare tutti gli appelli d'esame che avvengono nel pomeriggio (dopo le 14) del 20/06/2020 (21)

SELECT * 
FROM `exams` 
WHERE date= '2020-06-20' AND HOUR(hour) >= 14;

//6. Selezionare tutti i corsi di laurea magistrale (38)

SELECT * 
FROM `degrees` 
WHERE level = 'magistrale';

//7. Da quanti dipartimenti è composta l'università? (12)

SELECT COUNT(id) AS numero_dipartimenti
FROM `departments`

//8. Quanti sono gli insegnanti che non hanno un numero di telefono? (50)

SELECT * 
FROM `teachers` 
WHERE phone IS null;


// GROUP BY

// 1. Contare quanti iscritti ci sono stati ogni anno

SELECT YEAR(enrolment_date) AS Anno, COUNT(*) AS numero_iscritti
FROM `students`
GROUP BY YEAR(enrolment_date);

// 2. Contare gli insegnanti che hanno l'ufficio nello stesso edificio

SELECT office_address AS Sede, COUNT(*) AS numero_insegnanti
FROM `teachers` 
GROUP BY office_address;

// 3. Calcolare la media dei voti di ogni appello d'esame

SELECT exam_id AS Appello, AVG(vote) as Media
FROM `exam_student` 
GROUP BY exam_id;

// 4. Contare quanti corsi di laurea ci sono per ogni dipartimento

SELECT department_id, COUNT(*) AS corsi_laurea
FROM `degrees` 
GROUP BY department_id;


// JOIN


// 1. Selezionare tutti gli studenti iscritti al Corso di Laurea in Economia

SELECT 'students.name' as 'Studenti', 'degrees.name' AS 'Corso_di_Laurea'
FROM `degrees`
JOIN `students`
ON `students.degree_id` = `degrees.id`
WHERE `degrees.name` = 'Corso di Laurea in Economia';

// 2. Selezionare tutti i Corsi di Laurea Magistrale del Dipartimento di Neuroscienze

SELECT `departments`.`name` AS Dipartimenti, `degrees`.`name` AS Corso_di_Laurea, `degrees`.`level` AS Livello
FROM `departments`
JOIN `degrees`
ON `departments`.`id`= `department_id`
WHERE `departments`.`name` = 'Dipartimento di Neuroscienze'
AND `level` = 'magistrale';

// 3. Selezionare tutti i corsi in cui insegna Fulvio Amato (id=44)

SELECT `courses.name` AS Corso, `teachers.name` AS Nome, `teachers.surname` AS Cognome, `teachers.id` 
FROM `courses`
JOIN `course_teacher`
ON `course_teacher`.`course_id` = `courses`.`id`
JOIN `teachers`
ON `course_teacher`.`teacher_id` = `teachers`.`id`
WHERE `teacher_id`= 44;

// 4. Selezionare tutti gli studenti con i dati relativi al corso di laurea a cui
// sono iscritti e il relativo dipartimento, in ordine alfabetico per cognome e
// nome

SELECT `students`.`surname` AS "Cognome", `students`.`name` AS "Nome", `degrees`.*, `departments`.`name` AS "Dipartimento"
FROM `students`
JOIN `degrees`
ON `degrees`.`id` = `students`.`degree_id`
JOIN `departments`
ON `departments`.`id` =`degree_id`  
ORDER BY `Cognome` ASC

// 5. Selezionare tutti i corsi di laurea con i relativi corsi e insegnanti

SELECT `degrees`.id, `degrees`.name, `teachers`.name, `teachers`.surname, `teachers`.id, `courses`.name, `courses`.id
FROM `degrees`
JOIN `courses`
ON `degrees`.`id` = `degree_id`
JOIN `course_teacher`
ON `course_id` = `courses`.`id`
JOIN `teachers`
ON `teacher_id` = `teachers`.`id`
ORDER BY `degrees`.id;

// 6. Selezionare tutti i docenti che insegnano nel Dipartimento di Matematica (54)

SELECT DISTINCT `teachers`.`id`, `departments`.`name`, `teachers`.`name` AS "Nome", `teachers`.`surname` AS "Cognome"
FROM `teachers`
JOIN `course_teacher`
ON `teachers`.`id` = `teacher_id`
JOIN `courses`
ON `course_id` = `courses`.`id`
JOIN `degrees`
ON `degree_id` = `degrees`.`id`
JOIN `departments`
ON `departments`.`id` = `department_id`
WHERE `departments`.`id` = 5;

// 7. BONUS: Selezionare per ogni studente il numero di tentativi sostenuti per ogni esame, stampando anche il voto massimo. Successivamente,
// filtrare i tentativi con voto minimo 18.

SELECT `students`.`name`, `students`.`surname`, `students`.`id`, `courses`.`name`, COUNT(`exam_student`.`vote`) AS Tentativi, MAX(`exam_student`.`vote`) AS Voto_massimo
FROM `students`
JOIN `exam_student` 
ON `students`.`id `= `student_id`
JOIN `exams `
ON `exam_id` = `exams`.`id`
JOIN courses 
ON  `course_id` = `courses`.`id`
GROUP BY `students`.`id`, `courses`.`id`
HAVING (max_vote) >= 18  
ORDER BY `exam_count` DESC;