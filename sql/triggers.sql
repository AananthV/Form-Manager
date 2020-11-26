DELIMITER //

-- Update users.forms when a user creates a form
CREATE TRIGGER update_no_user_forms
AFTER INSERT ON forms FOR EACH ROW
BEGIN
    UPDATE users 
    SET users.forms = users.forms + 1
    WHERE users.id = NEW.owner;
END //

-- Update users.answers when a user answers a form
CREATE TRIGGER update_no_user_answers
AFTER INSERT ON answers FOR EACH ROW
BEGIN
    UPDATE users 
    SET users.answers = users.answers + 1
    WHERE users.id = NEW.user;
END //

-- Update forms.questions when a question is added to a form
CREATE TRIGGER update_no_form_questions
AFTER INSERT ON questions FOR EACH ROW
BEGIN
    UPDATE forms 
    SET forms.questions = forms.questions + 1
    WHERE forms.id = NEW.parent_form;
END //

-- Update forms.answers when a user answers a form
CREATE TRIGGER update_no_form_answers
AFTER INSERT ON answers FOR EACH ROW
BEGIN
    UPDATE forms 
    SET forms.answers = forms.answers + 1
    WHERE forms.id = NEW.form;
END //

-- Checks to prevent updatation of ID field
CREATE TRIGGER check_id_users
BEFORE UPDATE ON users FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change user ID';
    END IF;
END //

CREATE TRIGGER check_id_forms
BEFORE UPDATE ON forms FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change form ID';
    END IF;
END //

CREATE TRIGGER check_id_questions
BEFORE UPDATE ON questions FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change question ID';
    END IF;
END //

CREATE TRIGGER check_id_choices
BEFORE UPDATE ON choices FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change choice ID';
    END IF;
END //

CREATE TRIGGER check_id_notifications
BEFORE UPDATE ON notifications FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change notification ID';
    END IF;
END //

CREATE TRIGGER check_id_answers
BEFORE UPDATE ON answers FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change answer ID';
    END IF;
END //

CREATE TRIGGER check_id_choice_answers
BEFORE UPDATE ON choice_answers FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change choice_answer ID';
    END IF;
END //

CREATE TRIGGER check_id_short_text_answers
BEFORE UPDATE ON short_text_answers FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change short_text_answer ID';
    END IF;
END //

CREATE TRIGGER check_id_long_text_answers
BEFORE UPDATE ON long_text_answers FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change long_text_answer ID';
    END IF;
END //

CREATE TRIGGER check_id_validation
BEFORE UPDATE ON validation FOR EACH ROW
BEGIN
    IF NEW.id != OLD.id THEN
        SIGNAL SQLSTATE '45000' set message_text = 'Can not change validation ID';
    END IF;
END //

DELIMITER ;