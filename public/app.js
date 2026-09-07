const tabs = document.querySelectorAll("[data-tabs]");

tabs.forEach((tabsRoot) => {
  const buttons = tabsRoot.querySelectorAll("[data-tab-button]");
  const panels = tabsRoot.querySelectorAll("[data-tab-panel]");

  buttons.forEach((button) => {
    button.addEventListener("click", () => {
      const target = button.getAttribute("data-target");

      buttons.forEach((item) => item.classList.remove("is-active"));
      panels.forEach((panel) => panel.classList.remove("is-active"));

      button.classList.add("is-active");

      const activePanel = tabsRoot.querySelector(`[data-tab-panel="${target}"]`);
      if (activePanel) {
        activePanel.classList.add("is-active");
      }
    });
  });
});

const passwordToggles = document.querySelectorAll("[data-password-toggle]");

passwordToggles.forEach((toggle) => {
  toggle.addEventListener("click", () => {
    const wrapper = toggle.closest(".simple-password-wrap");
    const input = wrapper?.querySelector("input");

    if (!input) {
      return;
    }

    const nextType = input.type === "password" ? "text" : "password";
    input.type = nextType;

    const showLabel = toggle.getAttribute("data-show-label");
    const hideLabel = toggle.getAttribute("data-hide-label");

    toggle.setAttribute(
      "aria-label",
      nextType === "password" ? (showLabel || "Show password") : (hideLabel || "Hide password"),
    );

    toggle.classList.toggle("is-active", nextType === "text");
  });
});

const flyoutMenus = document.querySelectorAll("[data-flyout-menu]");

const closeFlyoutMenu = (menu) => {
  const dropdown = menu.querySelector("[data-flyout-dropdown]");
  const toggle = menu.querySelector("[data-flyout-toggle]");

  menu.classList.remove("is-open");

  if (dropdown) {
    dropdown.setAttribute("hidden", "");
  }

  if (toggle) {
    toggle.setAttribute("aria-expanded", "false");
  }
};

flyoutMenus.forEach((menu) => {
  const toggle = menu.querySelector("[data-flyout-toggle]");
  const dropdown = menu.querySelector("[data-flyout-dropdown]");

  if (!toggle || !dropdown) {
    return;
  }

  toggle.addEventListener("click", (event) => {
    event.preventDefault();
    event.stopPropagation();

    const isOpen = !dropdown.hasAttribute("hidden");

    flyoutMenus.forEach((item) => closeFlyoutMenu(item));

    if (!isOpen) {
      menu.classList.add("is-open");
      dropdown.removeAttribute("hidden");
      toggle.setAttribute("aria-expanded", "true");
    }
  });

  menu.addEventListener("mouseenter", () => {
    if (isMobileViewport()) {
      return;
    }

    flyoutMenus.forEach((item) => {
      if (item !== menu) {
        closeFlyoutMenu(item);
      }
    });

    menu.classList.add("is-open");
    dropdown.removeAttribute("hidden");
    toggle.setAttribute("aria-expanded", "true");
  });

  menu.addEventListener("mouseleave", () => {
    if (isMobileViewport()) {
      return;
    }

    closeFlyoutMenu(menu);
  });
});

document.addEventListener("click", (event) => {
  flyoutMenus.forEach((menu) => {
    if (menu.contains(event.target)) {
      return;
    }

    closeFlyoutMenu(menu);
  });
});

const topbarMenus = document.querySelectorAll("[data-topbar-menu]");
const isMobileViewport = () => window.matchMedia("(max-width: 767px)").matches;

const closeTopbarSubmenus = (menu) => {
  menu.querySelectorAll("[data-topbar-submenu]").forEach((submenu) => {
    submenu.classList.remove("is-open");
  });
};

const closeTopbarMenu = (menu) => {
  menu.classList.remove("is-open");

  const toggle = menu.querySelector("[data-topbar-menu-toggle]");
  if (toggle) {
    toggle.setAttribute("aria-expanded", "false");
  }

  closeTopbarSubmenus(menu);
};

topbarMenus.forEach((menu) => {
  const toggle = menu.querySelector("[data-topbar-menu-toggle]");
  const dropdown = menu.querySelector("[data-topbar-menu-dropdown]");

  if (!toggle || !dropdown) {
    return;
  }

  toggle.addEventListener("click", (event) => {
    if (!isMobileViewport()) {
      return;
    }

    event.preventDefault();
    const isOpen = menu.classList.contains("is-open");

    topbarMenus.forEach((item) => closeTopbarMenu(item));

    if (!isOpen) {
      menu.classList.add("is-open");
      toggle.setAttribute("aria-expanded", "true");
    }
  });

  menu.querySelectorAll("[data-topbar-submenu]").forEach((submenu) => {
    const parent = submenu.querySelector("[data-topbar-submenu-parent]");
    const panel = submenu.querySelector("[data-topbar-submenu-panel]");

    if (!parent || !panel) {
      return;
    }

    parent.addEventListener("click", (event) => {
      if (!isMobileViewport()) {
        return;
      }

      const isOpen = submenu.classList.contains("is-open");

      if (!isOpen) {
        event.preventDefault();
        closeTopbarSubmenus(menu);
        submenu.classList.add("is-open");
      }
    });
  });
});

document.addEventListener("click", (event) => {
  topbarMenus.forEach((menu) => {
    if (menu.contains(event.target)) {
      return;
    }

    closeTopbarMenu(menu);
  });
});

const lessonAccordionItems = document.querySelectorAll("[data-lesson-accordion-item]");

lessonAccordionItems.forEach((item) => {
  const toggle = item.querySelector("[data-lesson-accordion-toggle]");
  const body = item.querySelector("[data-lesson-accordion-body]");

  if (!toggle || !body) {
    return;
  }

  toggle.addEventListener("click", () => {
    const isOpen = item.classList.contains("is-open");

    lessonAccordionItems.forEach((accordionItem) => {
      const accordionBody = accordionItem.querySelector("[data-lesson-accordion-body]");
      const accordionToggle = accordionItem.querySelector("[data-lesson-accordion-toggle]");

      accordionItem.classList.remove("is-open");

      if (accordionBody) {
        accordionBody.setAttribute("hidden", "");
      }

      if (accordionToggle) {
        accordionToggle.setAttribute("aria-expanded", "false");
      }
    });

    if (!isOpen) {
      item.classList.add("is-open");
      body.removeAttribute("hidden");
      toggle.setAttribute("aria-expanded", "true");
    }
  });
});

const lessonGroupToggles = document.querySelectorAll("[data-lesson-group-toggle]");

lessonGroupToggles.forEach((toggle) => {
  const group = toggle.closest(".student-lesson-group");
  const body = group?.querySelector("[data-lesson-group-body]");

  if (!group || !body) {
    return;
  }

  toggle.addEventListener("click", () => {
    const isOpen = !body.hasAttribute("hidden");

    if (isOpen) {
      body.setAttribute("hidden", "");
      toggle.setAttribute("aria-expanded", "false");
      group.classList.remove("is-open");
      return;
    }

    body.removeAttribute("hidden");
    toggle.setAttribute("aria-expanded", "true");
    group.classList.add("is-open");
  });
});

const welcomeRevealNodes = document.querySelectorAll(".welcome-scroll-reveal, .welcome-text-reveal");

if (welcomeRevealNodes.length > 0) {
  const revealNode = (node) => {
    node.classList.add("is-visible");
  };

  if ("IntersectionObserver" in window) {
    const welcomeObserver = new IntersectionObserver(
      (entries, observer) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          revealNode(entry.target);
          observer.unobserve(entry.target);
        });
      },
      {
        threshold: 0.12,
        rootMargin: "0px 0px -8% 0px",
      },
    );

    welcomeRevealNodes.forEach((node) => {
      welcomeObserver.observe(node);
    });
  } else {
    welcomeRevealNodes.forEach((node) => {
      revealNode(node);
    });
  }

  window.addEventListener("load", () => {
    welcomeRevealNodes.forEach((node) => {
      revealNode(node);
    });
  });
}

const adminContentForms = document.querySelectorAll("[data-admin-content-form]");

adminContentForms.forEach((form) => {
  const levels = JSON.parse(form.getAttribute("data-levels") || "[]");
  const assets = JSON.parse(form.getAttribute("data-assets") || "{}");
  const levelSelect = form.querySelector("[data-admin-level-select]");
  const lessonSelect = form.querySelector("[data-admin-lesson-select]");
  const partSelect = form.querySelector("[data-admin-part-select]");
  const accessSelect = form.querySelector("[data-admin-access-select]");
  const courseFields = form.querySelector("[data-admin-course-fields]");
  const exerciseFields = form.querySelector("[data-admin-exercise-fields]");
  const quizFields = form.querySelector("[data-admin-quiz-fields]");
  const courseDescriptionInput = form.querySelector("[data-admin-course-description-input]");
  const courseSupportInput = form.querySelector("[data-admin-course-support-input]");
  const exerciseDescriptionInput = form.querySelector("[data-admin-exercise-description-input]");
  const exerciseSupportInput = form.querySelector("[data-admin-exercise-support-input]");
  const quizBodyInput = form.querySelector("[data-admin-quiz-body]");
  const quizBuilder = form.querySelector(".admin-quiz-builder--questions");
  const quizQuestionList = form.querySelector("[data-admin-quiz-question-list]");
  const addQuizQuestionButton = form.querySelector("[data-admin-add-quiz-question]");
  const mediaState = form.querySelector("[data-admin-media-state]");
  const coursePill = form.querySelector("[data-admin-course-pill]");
  const exercisePill = form.querySelector("[data-admin-exercise-pill]");

  if (!levelSelect || !lessonSelect || !partSelect || !accessSelect) {
    return;
  }

  const initialLesson = lessonSelect.dataset.selected || "";
  let hasLoadedInitialLesson = false;
  let isPrefilling = false;
  let isInitialHydration = true;

  const quizQuestionLabel = quizBuilder?.getAttribute("data-quiz-question-label") || "Question";
  const quizChoiceLabel = quizBuilder?.getAttribute("data-quiz-choice-label") || "Choix";
  const quizCorrectLabel = quizBuilder?.getAttribute("data-quiz-correct-label") || "Bonne reponse";
  const quizAddChoiceLabel = quizBuilder?.getAttribute("data-quiz-add-choice-label") || "Ajouter un choix";
  const quizDeleteQuestionLabel = quizBuilder?.getAttribute("data-quiz-delete-question-label") || "Supprimer la question";
  const quizQuestionImageLabel = quizBuilder?.getAttribute("data-quiz-question-image-label") || "Question image";
  const quizChoiceImageLabel = quizBuilder?.getAttribute("data-quiz-choice-image-label") || "Choice image";
  const quizKeepImageLabel = quizBuilder?.getAttribute("data-quiz-keep-image-label") || "Keep current image";

  const getQuizQuestionCards = () => Array.from(form.querySelectorAll("[data-admin-quiz-question-card]"));
  const getQuestionChoiceItems = (card) => Array.from(card.querySelectorAll("[data-admin-quiz-choice-item]"));
  const getQuestionChoiceInputs = (card) => getQuestionChoiceItems(card)
    .map((item) => item.querySelector("[data-admin-quiz-choice-input]"))
    .filter(Boolean);

  const parseQuizPayload = (rawValue) => {
    if (!rawValue) {
      return [{ question: "", choices: ["", ""], correct: "" }];
    }

    try {
      const parsed = JSON.parse(rawValue);

      if (Array.isArray(parsed?.questions)) {
        const questions = parsed.questions
          .filter((item) => item && typeof item === "object")
          .map((item) => ({
            question: item.question || "",
            question_image_path: item.question_image_path || "",
            question_image_url: item.question_image_url || "",
            choices: Array.isArray(item.choices)
              ? item.choices
                .filter((choice) => choice !== null && choice !== undefined)
                .map((choice) => (typeof choice === "string"
                  ? { text: choice, image_path: "", image_url: "" }
                  : {
                    text: choice.text || "",
                    image_path: choice.image_path || "",
                    image_url: choice.image_url || "",
                  }))
              : [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }],
            correct: item.correct ?? "",
          }));

        return questions.length ? questions : [{ question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }];
      }

      if (parsed && typeof parsed === "object" && Object.prototype.hasOwnProperty.call(parsed, "question")) {
        return [{
          question: parsed.question || "",
          question_image_path: parsed.question_image_path || "",
          question_image_url: parsed.question_image_url || "",
          choices: Array.isArray(parsed.choices)
            ? parsed.choices
              .filter((choice) => choice !== null && choice !== undefined)
              .map((choice) => (typeof choice === "string"
                ? { text: choice, image_path: "", image_url: "" }
                : {
                  text: choice.text || "",
                  image_path: choice.image_path || "",
                  image_url: choice.image_url || "",
                }))
            : [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }],
          correct: parsed.correct ?? "",
        }];
      }
    } catch {
      // Ignore malformed data and rebuild a clean quiz.
    }

    return [{ question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }];
  };

  const refreshQuestionChoiceLabels = (card) => {
    getQuestionChoiceItems(card).forEach((item, index) => {
      const label = item.querySelector("[data-admin-quiz-choice-label]");
      const input = item.querySelector("[data-admin-quiz-choice-input]");
      if (label) {
        label.textContent = `${quizChoiceLabel} ${index + 1}`;
      }
      if (input) {
        input.setAttribute("data-admin-quiz-choice-input", String(index));
      }
    });
  };

  const refreshQuestionCorrectOptions = (card) => {
    const correctSelect = card.querySelector("[data-admin-quiz-correct-input]");
    if (!correctSelect) {
      return;
    }

    const previousValue = correctSelect.value;
    const choiceInputs = getQuestionChoiceInputs(card);
    correctSelect.innerHTML = "";

    const emptyOption = document.createElement("option");
    emptyOption.value = "";
    emptyOption.textContent = "--";
    correctSelect.append(emptyOption);

    choiceInputs.forEach((input, index) => {
      const option = document.createElement("option");
      option.value = String(index);
      option.textContent = input.value.trim() || `${quizChoiceLabel} ${index + 1}`;
      correctSelect.append(option);
    });

    const hasPreviousValue = Array.from(correctSelect.options).some((option) => option.value === previousValue);
    correctSelect.value = hasPreviousValue ? previousValue : "";
  };

  const refreshQuestionRemoveButtons = (card) => {
    const choiceItems = getQuestionChoiceItems(card);
    choiceItems.forEach((item, index) => {
      const removeButton = item.querySelector("[data-admin-remove-quiz-choice]");
      if (!removeButton) {
        return;
      }
      removeButton.hidden = choiceItems.length <= 2 || index < 2;
    });
  };

  const refreshQuizQuestionHeadings = () => {
    const cards = getQuizQuestionCards();
    cards.forEach((card, index) => {
      const title = card.querySelector("[data-admin-quiz-question-title]");
      const removeButton = card.querySelector("[data-admin-remove-quiz-question]");
      if (title) {
        title.textContent = `${quizQuestionLabel} ${index + 1}`;
      }
      if (removeButton) {
        removeButton.hidden = cards.length <= 1;
      }
    });
  };

  const syncQuizPayload = () => {
    if (!quizBodyInput) {
      return;
    }

    const questions = getQuizQuestionCards().map((card) => {
      const questionInput = card.querySelector("[data-admin-quiz-question-input]");
      const correctSelect = card.querySelector("[data-admin-quiz-correct-input]");

      return {
        question: questionInput?.value.trim() || "",
        question_image_path: card.querySelector("[data-admin-quiz-question-image-path]")?.value || "",
        choices: getQuestionChoiceItems(card).map((item) => ({
          text: item.querySelector("[data-admin-quiz-choice-input]")?.value.trim() || "",
          image_path: item.querySelector("[data-admin-quiz-choice-image-path]")?.value || "",
        })).filter((choice) => choice.text.length > 0 || choice.image_path.length > 0),
        correct: correctSelect?.value ?? "",
      };
    }).filter((item) => item.question.length > 0 || item.choices.length > 0);

    quizBodyInput.value = JSON.stringify({
      questions: questions.length ? questions : [{ question: "", question_image_path: "", choices: [{ text: "", image_path: "" }, { text: "", image_path: "" }], correct: "" }],
    });
  };

  const refreshQuestionFileInputNames = (card) => {
    const questionIndex = getQuizQuestionCards().indexOf(card);
    const questionImageInput = card.querySelector("[data-admin-quiz-question-image-input]");
    if (questionImageInput) {
      questionImageInput.name = `quiz_question_images[${questionIndex}]`;
    }

    getQuestionChoiceItems(card).forEach((item, choiceIndex) => {
      const choiceImageInput = item.querySelector("[data-admin-quiz-choice-image-input]");
      if (choiceImageInput) {
        choiceImageInput.name = `quiz_choice_images[${questionIndex}][${choiceIndex}]`;
      }
    });
  };

  const createQuestionChoiceItem = (card, choiceData = { text: "", image_path: "", image_url: "" }) => {
    const list = card.querySelector("[data-admin-quiz-choice-list]");
    if (!list) {
      return;
    }

    const item = document.createElement("label");
    item.className = "field admin-quiz-choice-item";
    item.setAttribute("data-admin-quiz-choice-item", "");
    item.innerHTML = `
      <span data-admin-quiz-choice-label></span>
      <div class="admin-quiz-choice-row">
        <input type="text" value="">
        <button class="admin-quiz-choice-remove" type="button" data-admin-remove-quiz-choice>&times;</button>
      </div>
      <input type="hidden" value="" data-admin-quiz-choice-image-path>
      <label class="field admin-quiz-image-field">
        <span>${quizChoiceImageLabel}</span>
        <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" data-admin-quiz-choice-image-input>
      </label>
      <div class="admin-quiz-image-meta" data-admin-quiz-choice-image-meta hidden>
        <span>${quizKeepImageLabel}</span>
        <a href="#" target="_blank" rel="noopener noreferrer" data-admin-quiz-choice-image-link>Preview</a>
      </div>
    `;

    const input = item.querySelector("input");
    const removeButton = item.querySelector("[data-admin-remove-quiz-choice]");
    const imagePathInput = item.querySelector("[data-admin-quiz-choice-image-path]");
    const imageInput = item.querySelector("[data-admin-quiz-choice-image-input]");
    const imageMeta = item.querySelector("[data-admin-quiz-choice-image-meta]");
    const imageLink = item.querySelector("[data-admin-quiz-choice-image-link]");

    if (input) {
      input.value = choiceData.text || "";
      input.setAttribute("data-admin-quiz-choice-input", "");
      input.addEventListener("input", () => {
        refreshQuestionCorrectOptions(card);
        syncQuizPayload();
      });
    }

    if (imagePathInput) {
      imagePathInput.value = choiceData.image_path || "";
    }

    if (imageInput) {
      imageInput.addEventListener("change", syncQuizPayload);
    }

    if (imageMeta && imageLink && choiceData.image_url) {
      imageMeta.hidden = false;
      imageLink.href = choiceData.image_url;
    }

    if (removeButton) {
      removeButton.addEventListener("click", () => {
        const choiceItems = getQuestionChoiceItems(card);
        if (choiceItems.length <= 2) {
          return;
        }

        const removedIndex = choiceItems.indexOf(item);
        item.remove();
        refreshQuestionChoiceLabels(card);
        refreshQuestionRemoveButtons(card);
        refreshQuestionCorrectOptions(card);

        const correctSelect = card.querySelector("[data-admin-quiz-correct-input]");
        if (correctSelect && correctSelect.value === String(removedIndex)) {
          correctSelect.value = "";
        } else if (correctSelect && correctSelect.value !== "") {
          const currentValue = Number.parseInt(correctSelect.value, 10);
          if (!Number.isNaN(currentValue) && currentValue > removedIndex) {
            correctSelect.value = String(currentValue - 1);
          }
        }

        syncQuizPayload();
      });
    }

    list.append(item);
    refreshQuestionChoiceLabels(card);
    refreshQuestionRemoveButtons(card);
    refreshQuestionCorrectOptions(card);
    refreshQuestionFileInputNames(card);
  };

  const createQuizQuestionCard = (questionData = { question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }) => {
    if (!quizQuestionList) {
      return;
    }

    const card = document.createElement("section");
    card.className = "admin-quiz-question-card";
    card.setAttribute("data-admin-quiz-question-card", "");
    card.innerHTML = `
      <div class="admin-quiz-question-card__head">
        <strong data-admin-quiz-question-title></strong>
        <button class="admin-quiz-question-remove" type="button" data-admin-remove-quiz-question>${quizDeleteQuestionLabel}</button>
      </div>
      <label class="field">
        <span>${quizQuestionLabel}</span>
        <input type="text" value="" data-admin-quiz-question-input>
      </label>
      <input type="hidden" value="" data-admin-quiz-question-image-path>
      <label class="field admin-quiz-image-field">
        <span>${quizQuestionImageLabel}</span>
        <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" data-admin-quiz-question-image-input>
      </label>
      <div class="admin-quiz-image-meta" data-admin-quiz-question-image-meta hidden>
        <span>${quizKeepImageLabel}</span>
        <a href="#" target="_blank" rel="noopener noreferrer" data-admin-quiz-question-image-link>Preview</a>
      </div>
      <div class="admin-quiz-builder">
        <div class="admin-quiz-builder__head">
          <span>${quizChoiceLabel}</span>
          <button class="secondary-btn admin-quiz-builder__add" type="button" data-admin-add-quiz-choice>${quizAddChoiceLabel}</button>
        </div>
        <div class="admin-quiz-choice-list" data-admin-quiz-choice-list></div>
      </div>
      <label class="field">
        <span>${quizCorrectLabel}</span>
        <select data-admin-quiz-correct-input></select>
      </label>
    `;

    quizQuestionList.append(card);

    const questionInput = card.querySelector("[data-admin-quiz-question-input]");
    const addChoiceButton = card.querySelector("[data-admin-add-quiz-choice]");
    const removeQuestionButton = card.querySelector("[data-admin-remove-quiz-question]");
    const correctSelect = card.querySelector("[data-admin-quiz-correct-input]");
    const questionImagePathInput = card.querySelector("[data-admin-quiz-question-image-path]");
    const questionImageInput = card.querySelector("[data-admin-quiz-question-image-input]");
    const questionImageMeta = card.querySelector("[data-admin-quiz-question-image-meta]");
    const questionImageLink = card.querySelector("[data-admin-quiz-question-image-link]");

    if (questionInput) {
      questionInput.value = questionData.question || "";
      questionInput.addEventListener("input", syncQuizPayload);
    }

    if (questionImagePathInput) {
      questionImagePathInput.value = questionData.question_image_path || "";
    }

    if (questionImageInput) {
      questionImageInput.addEventListener("change", syncQuizPayload);
    }

    if (questionImageMeta && questionImageLink && questionData.question_image_url) {
      questionImageMeta.hidden = false;
      questionImageLink.href = questionData.question_image_url;
    }

    const normalizedChoices = Array.isArray(questionData.choices) && questionData.choices.length >= 2
      ? questionData.choices
      : [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }];

    normalizedChoices.forEach((choice) => {
      createQuestionChoiceItem(card, typeof choice === "string" ? { text: choice, image_path: "", image_url: "" } : choice);
    });

    if (correctSelect) {
      refreshQuestionCorrectOptions(card);
      correctSelect.value = String(questionData.correct ?? "");
      correctSelect.addEventListener("change", syncQuizPayload);
    }

    if (addChoiceButton) {
      addChoiceButton.addEventListener("click", () => {
        createQuestionChoiceItem(card, "");
        syncQuizPayload();
      });
    }

    if (removeQuestionButton) {
      removeQuestionButton.addEventListener("click", () => {
        if (getQuizQuestionCards().length <= 1) {
          return;
        }
        card.remove();
        refreshQuizQuestionHeadings();
        getQuizQuestionCards().forEach((currentCard) => refreshQuestionFileInputNames(currentCard));
        syncQuizPayload();
      });
    }

    refreshQuizQuestionHeadings();
    refreshQuestionFileInputNames(card);
    syncQuizPayload();
  };

  const populateLessons = () => {
    const level = levels.find((item) => item.key === levelSelect.value) || levels[0];
    const previousValue = lessonSelect.value;
    const targetValue = (!hasLoadedInitialLesson && initialLesson) ? initialLesson : previousValue;

    lessonSelect.innerHTML = "";

    if (!level) {
      const option = document.createElement("option");
      option.value = "";
      option.textContent = "--";
      lessonSelect.append(option);
      return;
    }

    level.lessons.forEach((lesson) => {
      const option = document.createElement("option");
      option.value = lesson.slug;
      option.textContent = lesson.label;
      lessonSelect.append(option);
    });

    const hasTarget = Array.from(lessonSelect.options).some((option) => option.value === targetValue);
    lessonSelect.value = hasTarget ? targetValue : (lessonSelect.options[0]?.value || "");
    hasLoadedInitialLesson = true;
  };

  const syncPartFields = () => {
    const activePart = partSelect.value;
    const isCourse = activePart === "course";
    const isExercise = activePart === "exercise";
    const isQuiz = activePart === "quiz";

    if (courseFields) {
      courseFields.hidden = !isCourse;
      courseFields.querySelectorAll("input, textarea, select").forEach((field) => {
        if (
          field.name === "video_file" ||
          field.name === "support_file" ||
          field.name === "description_body" ||
          field.name === "support_body" ||
          field.name === "remove_video" ||
          field.name === "remove_support_file" ||
          field.name === "clear_description" ||
          field.name === "clear_support_body"
        ) {
          field.disabled = !isCourse;
        }
      });
    }

    if (exerciseFields) {
      exerciseFields.hidden = !isExercise;
      exerciseFields.querySelectorAll("input, textarea, select").forEach((field) => {
        if (
          field.name === "video_file" ||
          field.name === "support_file" ||
          field.name === "description_body" ||
          field.name === "support_body" ||
          field.name === "remove_video" ||
          field.name === "remove_support_file" ||
          field.name === "clear_description" ||
          field.name === "clear_support_body"
        ) {
          field.disabled = !isExercise;
        }
      });
    }

    if (quizFields) {
      quizFields.hidden = !isQuiz;
      quizFields.querySelectorAll("textarea, input, select").forEach((field) => {
        field.disabled = !isQuiz && field !== quizBodyInput;
      });
    }
  };

  const fillAssetState = () => {
    const assetKey = `${lessonSelect.value}|${partSelect.value}`;
    const asset = assets[assetKey] || null;

    if (mediaState) {
      mediaState.innerHTML = "";
      const pill = document.createElement("span");
      pill.className = "ghost-pill";
      pill.textContent = asset ? `${asset.content_type || "content"} • ${asset.access_level || "free"}` : "New content";
      mediaState.append(pill);
    }

    if (coursePill) {
      coursePill.textContent = asset?.has_media ? "Media saved" : "No media saved";
    }

    if (exercisePill) {
      exercisePill.textContent = asset?.has_media ? "Media saved" : "No media saved";
    }

    if (!isPrefilling) {
      return;
    }

    if (isInitialHydration && accessSelect.value) {
      // Keep server-rendered value on the first pass, especially after validation errors.
    } else {
      accessSelect.value = asset?.access_level || "free";
    }

    if (courseDescriptionInput && partSelect.value === "course") {
      if (!isInitialHydration || !courseDescriptionInput.value.trim()) {
        courseDescriptionInput.value = asset?.description_body || "";
      }
    }

    if (courseSupportInput && partSelect.value === "course") {
      if (!isInitialHydration || !courseSupportInput.value.trim()) {
        courseSupportInput.value = asset?.support_body || "";
      }
    }

    if (exerciseDescriptionInput && partSelect.value === "exercise") {
      if (!isInitialHydration || !exerciseDescriptionInput.value.trim()) {
        exerciseDescriptionInput.value = asset?.description_body || "";
      }
    }

    if (exerciseSupportInput && partSelect.value === "exercise") {
      if (!isInitialHydration || !exerciseSupportInput.value.trim()) {
        exerciseSupportInput.value = asset?.support_body || "";
      }
    }

    if (quizBodyInput && quizQuestionList) {
      const rawQuizPayload = isInitialHydration && quizBodyInput.value
        ? quizBodyInput.value
        : (asset?.quiz_body || "");
      const questions = parseQuizPayload(rawQuizPayload);

      quizQuestionList.innerHTML = "";
      questions.forEach((question) => createQuizQuestionCard(question));
      refreshQuizQuestionHeadings();
    }
  };

  const refreshForm = (shouldPrefill = true) => {
    isPrefilling = shouldPrefill;
    syncPartFields();
    fillAssetState();
    isPrefilling = false;
  };

  levelSelect.addEventListener("change", () => {
    populateLessons();
    refreshForm(true);
  });

  lessonSelect.addEventListener("change", () => {
    refreshForm(true);
  });

  partSelect.addEventListener("change", () => {
    refreshForm(true);
  });

  if (addQuizQuestionButton) {
    addQuizQuestionButton.addEventListener("click", () => {
      createQuizQuestionCard({ question: "", choices: ["", ""], correct: "" });
    });
  }

  form.addEventListener("submit", () => {
    syncQuizPayload();
  });

  populateLessons();
  refreshForm(true);
  syncQuizPayload();
  isInitialHydration = false;
});

const teacherContentForms = document.querySelectorAll("[data-teacher-content-form]");

teacherContentForms.forEach((form) => {
  const assets = JSON.parse(form.getAttribute("data-assets") || "{}");
  const programOptions = JSON.parse(form.getAttribute("data-program-options") || "[]");
  const localeSelect = form.querySelector("[data-teacher-locale-select]");
  const programSelect = form.querySelector("[data-teacher-program-select]");
  const trackSelect = form.querySelector("[data-teacher-track-select]");
  const subjectSelect = form.querySelector("[data-teacher-subject-select]");
  const levelKeyInput = form.querySelector("[data-teacher-level-key]");
  const lessonSelect = form.querySelector("[data-teacher-lesson-select]");
  const sortInput = form.querySelector("[data-teacher-sort-input]");
  const partSelect = form.querySelector("[data-teacher-part-select]");
  const accessSelect = form.querySelector("[data-teacher-access-select]");
  const deleteLessonInput = document.querySelector("[data-teacher-delete-lesson]");
  const deletePartInput = document.querySelector("[data-teacher-delete-part]");
  const deleteButton = document.querySelector("[data-teacher-delete-button]");
  const videoFields = form.querySelector("[data-teacher-video-fields]");
  const quizFields = form.querySelector("[data-teacher-quiz-fields]");
  const quizBodyInput = form.querySelector("[data-teacher-quiz-body]");
  const quizBuilder = form.querySelector("[data-teacher-quiz-builder]");
  const quizQuestionList = form.querySelector("[data-teacher-quiz-question-list]");
  const addQuestionButton = form.querySelector("[data-teacher-add-question]");
  const youtubeInput = form.querySelector('input[name="youtube_url"]');
  const descriptionInput = form.querySelector('textarea[name="description_body"]');
  const supportInput = form.querySelector('textarea[name="support_body"]');

  if (!localeSelect || !programSelect || !trackSelect || !subjectSelect || !levelKeyInput || !lessonSelect || !partSelect || !accessSelect || !quizBodyInput || !quizBuilder || !quizQuestionList) {
    return;
  }

  let isPrefilling = true;

  const questionLabel = quizBuilder.getAttribute("data-question-label") || "Question";
  const choiceLabel = quizBuilder.getAttribute("data-choice-label") || "Choice";
  const correctLabel = quizBuilder.getAttribute("data-correct-label") || "Correct answer";
  const addChoiceLabel = quizBuilder.getAttribute("data-add-choice-label") || "Add choice";
  const addQuestionLabel = quizBuilder.getAttribute("data-add-question-label") || "Add question";
  const deleteQuestionLabel = quizBuilder.getAttribute("data-delete-question-label") || "Delete question";
  const questionImageLabel = quizBuilder.getAttribute("data-question-image-label") || "Question image";
  const choiceImageLabel = quizBuilder.getAttribute("data-choice-image-label") || "Choice image";
  const keepImageLabel = quizBuilder.getAttribute("data-keep-image-label") || "Keep current image";
  const chooseTrackLabel = trackSelect.querySelector("option")?.textContent || "Choose the track";
  const chooseSubjectLabel = subjectSelect.querySelector("option")?.textContent || "Choose the subject";

  const fillSelect = (select, items, defaultLabel, selectedValue) => {
    select.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = defaultLabel;
    select.append(defaultOption);

    const selectedValues = Array.isArray(selectedValue) ? selectedValue : [selectedValue].filter(Boolean);
    items.forEach((item) => {
      const option = document.createElement("option");
      option.value = item.key;
      option.textContent = item.label;
      option.selected = selectedValues.includes(item.key);
      select.append(option);
    });
  };

  const syncProgramTrackSubject = ({ preserveTrack = true, preserveSubject = true } = {}) => {
    const activeProgram = programOptions.find((item) => item.key === programSelect.value) || null;
    const currentTrack = preserveTrack ? Array.from(trackSelect.selectedOptions).map((option) => option.value).filter(Boolean) : [];
    const currentSubject = preserveSubject ? subjectSelect.value : "";
    const tracks = activeProgram ? activeProgram.tracks || [] : [];
    const hasTracks = tracks.length > 0;

    fillSelect(trackSelect, tracks, chooseTrackLabel, hasTracks ? currentTrack : "");
    trackSelect.disabled = !activeProgram || !hasTracks;
    const trackOptions = form.querySelector('[data-teacher-track-options]');
    if (trackOptions) {
      trackOptions.replaceChildren();
      Array.from(trackSelect.options).filter((option) => option.value).forEach((option) => {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = option.selected;
        checkbox.disabled = trackSelect.disabled;
        checkbox.addEventListener('change', () => {
          option.selected = checkbox.checked;
          trackSelect.dispatchEvent(new Event('change', { bubbles: true }));
        });
        label.append(checkbox, document.createTextNode(option.textContent));
        trackOptions.append(label);
      });
    }
    trackSelect.closest("label")?.classList.toggle("is-disabled", trackSelect.disabled);
    const trackHint = form.querySelector("[data-teacher-track-hint]");
    if (trackHint) {
      trackHint.hidden = !(activeProgram && !hasTracks);
    }
    if (!hasTracks) {
      Array.from(trackSelect.options).forEach((option) => {
        option.selected = false;
      });
    }

    const selectedTracks = hasTracks
      ? Array.from(trackSelect.selectedOptions).map((option) => option.value).filter(Boolean)
      : [];
    const activeTrack = selectedTracks.length > 0
      ? tracks.find((item) => item.key === selectedTracks[0]) || null
      : null;
    const subjects = selectedTracks.length > 0
      ? tracks.find((item) => item.key === selectedTracks[0]).subjects.filter((subject) => selectedTracks.every((trackKey) => tracks.find((item) => item.key === trackKey).subjects.some((item) => item.key === subject.key)))
      : (activeProgram ? activeProgram.subjects || [] : []);

    // Keep the current subject selected when it is still available after a level/track change.
    fillSelect(subjectSelect, subjects, chooseSubjectLabel, currentSubject);
    if (currentSubject && Array.from(subjectSelect.options).some((option) => option.value === currentSubject)) {
      subjectSelect.value = currentSubject;
    }
    subjectSelect.disabled = subjects.length === 0;

    if (activeProgram && activeTrack) {
      levelKeyInput.value = `${activeProgram.key}::${selectedTracks.join("|")}`;
    } else if (activeProgram) {
      levelKeyInput.value = activeProgram.key;
    } else {
      levelKeyInput.value = "";
    }
  };

  const getQuestionCards = () => Array.from(quizQuestionList.querySelectorAll("[data-teacher-question-card]"));
  const getChoiceItems = (card) => Array.from(card.querySelectorAll("[data-teacher-choice-item]"));
  const getChoiceInputs = (card) => getChoiceItems(card)
    .map((item) => item.querySelector("[data-teacher-choice-input]"))
    .filter(Boolean);

  const parseQuizPayload = (rawValue) => {
    if (!rawValue) {
      return [{ question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }];
    }

    try {
      const parsed = JSON.parse(rawValue);
      if (Array.isArray(parsed?.questions)) {
        return parsed.questions.map((item) => ({
          question: item.question || "",
          question_image_path: item.question_image_path || "",
          question_image_url: item.question_image_url || "",
          choices: Array.isArray(item.choices)
            ? item.choices.map((choice) => (typeof choice === "string"
              ? { text: choice, image_path: "", image_url: "" }
              : {
                text: choice?.text || "",
                image_path: choice?.image_path || "",
                image_url: choice?.image_url || "",
              }))
            : [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }],
          correct: item.correct ?? "",
        }));
      }
    } catch {
      // Ignore malformed quiz data and rebuild a clean quiz.
    }

    return [{ question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }];
  };

  const syncQuizPayload = () => {
    const questions = getQuestionCards().map((card) => ({
      question: card.querySelector("[data-teacher-question-input]")?.value.trim() || "",
      question_image_path: card.querySelector("[data-teacher-question-image-path]")?.value || "",
      choices: getChoiceItems(card)
        .map((item) => ({
          text: item.querySelector("[data-teacher-choice-input]")?.value.trim() || "",
          image_path: item.querySelector("[data-teacher-choice-image-path]")?.value || "",
        }))
        .filter((choice) => choice.text.length > 0 || choice.image_path.length > 0),
      correct: card.querySelector("[data-teacher-correct-input]")?.value || "",
    })).filter((item) => item.question.length > 0 || item.choices.length > 0);

    quizBodyInput.value = JSON.stringify({
      questions: questions.length ? questions : [{ question: "", question_image_path: "", choices: [{ text: "", image_path: "" }, { text: "", image_path: "" }], correct: "" }],
    });
  };

  const refreshQuestionTitles = () => {
    const cards = getQuestionCards();
    cards.forEach((card, index) => {
      const title = card.querySelector("[data-teacher-question-title]");
      const removeButton = card.querySelector("[data-teacher-remove-question]");
      if (title) {
        title.textContent = `${questionLabel} ${index + 1}`;
      }
      if (removeButton) {
        removeButton.hidden = cards.length <= 1;
      }
    });
  };

  const refreshQuestionFileInputNames = (card) => {
    const questionIndex = getQuestionCards().indexOf(card);
    const questionImageInput = card.querySelector("[data-teacher-question-image-input]");
    if (questionImageInput) {
      questionImageInput.name = `quiz_question_images[${questionIndex}]`;
    }

    getChoiceItems(card).forEach((item, choiceIndex) => {
      const choiceImageInput = item.querySelector("[data-teacher-choice-image-input]");
      if (choiceImageInput) {
        choiceImageInput.name = `quiz_choice_images[${questionIndex}][${choiceIndex}]`;
      }
    });
  };

  const refreshCorrectOptions = (card) => {
    const select = card.querySelector("[data-teacher-correct-input]");
    if (!select) {
      return;
    }

    const previousValue = select.value;
    select.innerHTML = "";

    const emptyOption = document.createElement("option");
    emptyOption.value = "";
    emptyOption.textContent = "--";
    select.append(emptyOption);

    getChoiceInputs(card).forEach((input, index) => {
      const option = document.createElement("option");
      option.value = String(index);
      option.textContent = input.value.trim() || `${choiceLabel} ${index + 1}`;
      select.append(option);
    });

    select.value = Array.from(select.options).some((option) => option.value === previousValue) ? previousValue : "";
  };

  const createChoiceItem = (card, choiceData = { text: "", image_path: "", image_url: "" }) => {
    const list = card.querySelector("[data-teacher-choice-list]");
    if (!list) {
      return;
    }

    const item = document.createElement("label");
    item.className = "field admin-quiz-choice-item";
    item.setAttribute("data-teacher-choice-item", "");
    item.innerHTML = `
      <span data-teacher-choice-label>${choiceLabel}</span>
      <div class="admin-quiz-choice-row">
        <input type="text" value="" data-teacher-choice-input>
        <button class="admin-quiz-choice-remove" type="button" data-teacher-remove-choice>&times;</button>
      </div>
      <input type="hidden" value="" data-teacher-choice-image-path>
      <label class="field admin-quiz-image-field">
        <span>${choiceImageLabel}</span>
        <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" data-teacher-choice-image-input>
      </label>
      <div class="admin-quiz-image-meta" data-teacher-choice-image-meta hidden>
        <span>${keepImageLabel}</span>
        <a href="#" target="_blank" rel="noopener noreferrer" data-teacher-choice-image-link>Preview</a>
      </div>
    `;

    const input = item.querySelector("[data-teacher-choice-input]");
    const removeButton = item.querySelector("[data-teacher-remove-choice]");
    const imagePathInput = item.querySelector("[data-teacher-choice-image-path]");
    const imageInput = item.querySelector("[data-teacher-choice-image-input]");
    const imageMeta = item.querySelector("[data-teacher-choice-image-meta]");
    const imageLink = item.querySelector("[data-teacher-choice-image-link]");

    if (input) {
      input.value = choiceData.text || "";
      input.addEventListener("input", () => {
        refreshCorrectOptions(card);
        syncQuizPayload();
      });
    }

    if (imagePathInput) {
      imagePathInput.value = choiceData.image_path || "";
    }

    if (imageInput) {
      imageInput.addEventListener("change", syncQuizPayload);
    }

    if (imageMeta && imageLink && choiceData.image_url) {
      imageMeta.hidden = false;
      imageLink.href = choiceData.image_url;
    }

    if (removeButton) {
      removeButton.addEventListener("click", () => {
        if (getChoiceItems(card).length <= 2) {
          return;
        }

        item.remove();
        getChoiceItems(card).forEach((choiceItem, index) => {
          const label = choiceItem.querySelector("[data-teacher-choice-label]");
          if (label) {
            label.textContent = `${choiceLabel} ${index + 1}`;
          }
        });
        refreshQuestionFileInputNames(card);
        refreshCorrectOptions(card);
        syncQuizPayload();
      });
    }

    list.append(item);
    getChoiceItems(card).forEach((choiceItem, index) => {
      const label = choiceItem.querySelector("[data-teacher-choice-label]");
      if (label) {
        label.textContent = `${choiceLabel} ${index + 1}`;
      }
    });
    refreshQuestionFileInputNames(card);
  };

  const createQuestionCard = (questionData = { question: "", question_image_path: "", question_image_url: "", choices: [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }], correct: "" }) => {
    const card = document.createElement("section");
    card.className = "admin-quiz-question-card";
    card.setAttribute("data-teacher-question-card", "");
    card.innerHTML = `
      <div class="admin-quiz-question-card__head">
        <strong data-teacher-question-title>${questionLabel}</strong>
        <button class="admin-quiz-question-remove" type="button" data-teacher-remove-question>${deleteQuestionLabel}</button>
      </div>
      <label class="field">
        <span>${questionLabel}</span>
        <input type="text" value="" data-teacher-question-input>
      </label>
      <input type="hidden" value="" data-teacher-question-image-path>
      <label class="field admin-quiz-image-field">
        <span>${questionImageLabel}</span>
        <input type="file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" data-teacher-question-image-input>
      </label>
      <div class="admin-quiz-image-meta" data-teacher-question-image-meta hidden>
        <span>${keepImageLabel}</span>
        <a href="#" target="_blank" rel="noopener noreferrer" data-teacher-question-image-link>Preview</a>
      </div>
      <div class="admin-quiz-builder">
        <div class="admin-quiz-builder__head">
          <span>${choiceLabel}</span>
          <button class="secondary-btn admin-quiz-builder__add" type="button" data-teacher-add-choice>${addChoiceLabel}</button>
        </div>
        <div class="admin-quiz-choice-list" data-teacher-choice-list></div>
      </div>
      <label class="field">
        <span>${correctLabel}</span>
        <select data-teacher-correct-input></select>
      </label>
    `;

    const questionInput = card.querySelector("[data-teacher-question-input]");
    const removeButton = card.querySelector("[data-teacher-remove-question]");
    const addChoiceButton = card.querySelector("[data-teacher-add-choice]");
    const correctSelect = card.querySelector("[data-teacher-correct-input]");
    const questionImagePathInput = card.querySelector("[data-teacher-question-image-path]");
    const questionImageInput = card.querySelector("[data-teacher-question-image-input]");
    const questionImageMeta = card.querySelector("[data-teacher-question-image-meta]");
    const questionImageLink = card.querySelector("[data-teacher-question-image-link]");

    if (questionInput) {
      questionInput.value = questionData.question || "";
      questionInput.addEventListener("input", syncQuizPayload);
    }

    if (questionImagePathInput) {
      questionImagePathInput.value = questionData.question_image_path || "";
    }

    if (questionImageInput) {
      questionImageInput.addEventListener("change", syncQuizPayload);
    }

    if (questionImageMeta && questionImageLink && questionData.question_image_url) {
      questionImageMeta.hidden = false;
      questionImageLink.href = questionData.question_image_url;
    }

    (Array.isArray(questionData.choices) && questionData.choices.length ? questionData.choices : [{ text: "", image_path: "", image_url: "" }, { text: "", image_path: "", image_url: "" }]).forEach((choice) => {
      createChoiceItem(card, choice);
    });

    if (correctSelect) {
      refreshCorrectOptions(card);
      correctSelect.value = String(questionData.correct ?? "");
      correctSelect.addEventListener("change", syncQuizPayload);
    }

    if (addChoiceButton) {
      addChoiceButton.addEventListener("click", () => {
        createChoiceItem(card, { text: "", image_path: "", image_url: "" });
        refreshCorrectOptions(card);
        syncQuizPayload();
      });
    }

    if (removeButton) {
      removeButton.addEventListener("click", () => {
        if (getQuestionCards().length <= 1) {
          return;
        }
        card.remove();
        refreshQuestionTitles();
        syncQuizPayload();
      });
    }

    quizQuestionList.append(card);
    refreshQuestionFileInputNames(card);
    refreshQuestionTitles();
    syncQuizPayload();
  };

  const syncPartFields = () => {
    const isQuiz = partSelect.value === "quiz";

    if (videoFields) {
      videoFields.hidden = isQuiz;
      videoFields.querySelectorAll("input, textarea").forEach((field) => {
        if (field.name === "youtube_url" || field.name === "description_body" || field.name === "support_body") {
          field.disabled = isQuiz;
        }
      });
    }

    if (quizFields) {
      quizFields.hidden = !isQuiz;
      quizFields.querySelectorAll("input, select, textarea, button").forEach((field) => {
        if (field !== quizBodyInput) {
          field.disabled = !isQuiz;
        }
      });
    }
  };

  const filterLessons = () => {
    const currentLocale = localeSelect.value;
    const currentSubject = subjectSelect.value;
    const currentLevel = levelKeyInput.value;

    Array.from(lessonSelect.options).forEach((option, index) => {
      if (index === 0) {
        return;
      }
      const matches = option.getAttribute("data-locale") === currentLocale
        && option.getAttribute("data-subject") === currentSubject
        && option.getAttribute("data-level") === currentLevel;
      option.hidden = !matches;
      option.disabled = !matches;
    });

    if (lessonSelect.value && lessonSelect.selectedOptions[0] && lessonSelect.selectedOptions[0].disabled) {
      lessonSelect.value = "";
    }
  };

  const syncDeleteState = () => {
    if (deleteLessonInput) {
      deleteLessonInput.value = lessonSelect.value || "";
    }

    if (deletePartInput) {
      deletePartInput.value = partSelect.value || "course";
    }

    if (deleteButton) {
      deleteButton.disabled = !lessonSelect.value;
    }
  };

  const fillAssetState = () => {
    const asset = assets[`${lessonSelect.value}|${partSelect.value}`] || null;

    if (!isPrefilling || !asset) {
      if (youtubeInput) youtubeInput.value = "";
      if (accessSelect) accessSelect.value = "free";
      if (descriptionInput) descriptionInput.value = "";
      if (supportInput) supportInput.value = "";
      quizQuestionList.innerHTML = "";
      createQuestionCard();
      return;
    }

    if (youtubeInput) {
      youtubeInput.value = asset.youtube_url || "";
    }
    if (accessSelect) {
      accessSelect.value = asset.access_level || "free";
    }
    if (descriptionInput) {
      descriptionInput.value = asset.description_body || "";
    }
    if (supportInput) {
      supportInput.value = asset.support_body || "";
    }

    quizQuestionList.innerHTML = "";
    parseQuizPayload(asset.quiz_body || "").forEach((question) => createQuestionCard(question));
  };

  const syncSelectionState = ({ preserveTrack = true, preserveSubject = true } = {}) => {
    syncProgramTrackSubject({ preserveTrack, preserveSubject });
    filterLessons();
    syncDeleteState();
    isPrefilling = true;
    fillAssetState();
    isPrefilling = false;
  };

  programSelect.addEventListener("change", () => {
    syncSelectionState({ preserveTrack: true, preserveSubject: true });
  });

  trackSelect.addEventListener("change", () => {
    syncSelectionState({ preserveTrack: true, preserveSubject: true });
  });

  localeSelect.addEventListener("change", () => {
    syncSelectionState({ preserveTrack: true, preserveSubject: true });
  });

  subjectSelect.addEventListener("change", () => {
    syncSelectionState({ preserveTrack: true, preserveSubject: true });
  });

  lessonSelect.addEventListener("change", () => {
    syncDeleteState();
    if (sortInput) {
      sortInput.value = lessonSelect.selectedOptions[0]?.getAttribute("data-sort-order") || "";
    }
    isPrefilling = true;
    fillAssetState();
    isPrefilling = false;
  });

  partSelect.addEventListener("change", () => {
    syncPartFields();
    syncDeleteState();
    isPrefilling = true;
    fillAssetState();
    isPrefilling = false;
  });

  if (addQuestionButton) {
    addQuestionButton.addEventListener("click", () => createQuestionCard());
  }

  form.addEventListener("submit", () => {
    syncQuizPayload();
  });

  syncProgramTrackSubject({ preserveTrack: true, preserveSubject: true });
  filterLessons();
  syncPartFields();
  syncDeleteState();
  isPrefilling = true;
  fillAssetState();
  isPrefilling = false;
});

const studentQuizForms = document.querySelectorAll("[data-student-quiz]");

studentQuizForms.forEach((form) => {
  const questions = Array.from(form.querySelectorAll("[data-student-quiz-question]"));
  const result = form.querySelector("[data-student-quiz-result]");

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    let score = 0;

    questions.forEach((question, index) => {
      const selected = form.querySelector(`input[name="student-quiz-${index}"]:checked`);
      const correctAnswer = question.getAttribute("data-correct-answer") || "";
      const options = Array.from(question.querySelectorAll("[data-student-quiz-option]"));

      options.forEach((option) => {
        option.classList.remove("is-correct", "is-wrong");
        const input = option.querySelector("input");
        const optionValue = input?.value || "";

        if (optionValue === correctAnswer) {
          option.classList.add("is-correct");
        }

        if (selected && input?.checked && optionValue !== correctAnswer) {
          option.classList.add("is-wrong");
        }
      });

      if (selected && selected.value === correctAnswer) {
        score += 1;
      }
    });

    if (result) {
      result.hidden = false;
      const value = result.querySelector("span");
      if (value) {
        value.textContent = `${score} / ${questions.length}`;
      } else {
        result.textContent = `${score} / ${questions.length}`;
      }
    }
  });
});

const subscriptionForms = document.querySelectorAll("[data-admin-subscription-form]");

subscriptionForms.forEach((form) => {
  const tierSelect = form.querySelector("[data-subscription-tier]");
  const durationField = form.querySelector("[data-premium-duration-field]");
  const levelField = form.querySelector("[data-premium-level-field]");
  const durationSelect = form.querySelector("[data-subscription-duration]");
  const levelSelect = form.querySelector("[data-subscription-level]");
  const previewDate = form.querySelector("[data-subscription-preview-date]");
  const todayValue = form.getAttribute("data-today");

  if (!tierSelect || !durationField || !levelField || !durationSelect || !levelSelect || !previewDate || !todayValue) {
    return;
  }

  const computeEndDate = () => {
    if (tierSelect.value !== "premium" || !durationSelect.value) {
      previewDate.textContent = "-";
      return;
    }

    const baseDate = new Date(`${todayValue}T12:00:00`);

    if (Number.isNaN(baseDate.getTime())) {
      previewDate.textContent = "-";
      return;
    }

    switch (durationSelect.value) {
      case "1_month":
        baseDate.setMonth(baseDate.getMonth() + 1);
        break;
      case "6_months":
        baseDate.setMonth(baseDate.getMonth() + 6);
        break;
      case "1_year":
        baseDate.setFullYear(baseDate.getFullYear() + 1);
        break;
      default:
        previewDate.textContent = "-";
        return;
    }

    const year = baseDate.getFullYear();
    const month = `${baseDate.getMonth() + 1}`.padStart(2, "0");
    const day = `${baseDate.getDate()}`.padStart(2, "0");
    previewDate.textContent = `${year}-${month}-${day}`;
  };

  const syncSubscriptionFields = () => {
    const isPremium = tierSelect.value === "premium";

    durationField.hidden = !isPremium;
    levelField.hidden = !isPremium;
    durationSelect.disabled = !isPremium;
    levelSelect.disabled = !isPremium;

    if (!isPremium) {
      durationSelect.value = "";
      levelSelect.value = "";
    }

    computeEndDate();
  };

  tierSelect.addEventListener("change", syncSubscriptionFields);
  durationSelect.addEventListener("change", computeEndDate);

  syncSubscriptionFields();
});

const teacherSpaceForms = document.querySelectorAll("[data-teacher-space-form]");

teacherSpaceForms.forEach((form) => {
  const partSelect = form.querySelector("[data-teacher-space-part]");
  const chip = form.querySelector("[data-teacher-space-chip]");

  if (!partSelect || !chip) {
    return;
  }

  const syncTeacherSpacePart = () => {
    const labels = {
      course: "COURS",
      exercise: "EXERCICE",
      quiz: "QUIZ",
    };

    chip.textContent = labels[partSelect.value] || "COURS";
  };

  partSelect.addEventListener("change", syncTeacherSpacePart);
  syncTeacherSpacePart();
});

const teacherDeletePanels = document.querySelectorAll("[data-teacher-delete-panel]");

teacherDeletePanels.forEach((panel) => {
  const programOptions = JSON.parse(panel.getAttribute("data-program-options") || "[]");
  const localeSelect = panel.querySelector("[data-teacher-delete-locale]");
  const levelSelect = panel.querySelector("[data-teacher-delete-level]");
  const trackSelect = panel.querySelector("[data-teacher-delete-track]");
  const subjectSelect = panel.querySelector("[data-teacher-delete-subject]");
  const typeSelect = panel.querySelector("[data-teacher-delete-type]");
  const emptyState = panel.querySelector("[data-teacher-delete-empty]");
  const resultsSection = document.querySelector("[data-teacher-delete-results]");
  const cards = Array.from(document.querySelectorAll("[data-teacher-delete-card]"));

  if (!localeSelect || !levelSelect || !trackSelect || !subjectSelect || !typeSelect || cards.length === 0) {
    return;
  }

  const trackDefaultLabel = trackSelect.querySelector("option")?.textContent || "";

  const fillTrackOptions = () => {
    const currentLevel = levelSelect.value;
      const currentTrack = trackSelect.value;
    const activeProgram = programOptions.find((item) => item.key === currentLevel) || null;
    const tracks = activeProgram ? activeProgram.tracks || [] : [];

    trackSelect.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.textContent = trackDefaultLabel;
    trackSelect.append(defaultOption);

    tracks.forEach((track) => {
      const option = document.createElement("option");
      option.value = track.key;
      option.textContent = track.label;
      option.selected = currentTrack === track.key;
      trackSelect.append(option);
    });

    const hasSelectedTrack = Array.from(trackSelect.options).some((option) => option.value === currentTrack);
    trackSelect.value = hasSelectedTrack ? currentTrack : "";
    trackSelect.disabled = !currentLevel || tracks.length === 0;
  };

  const applyFilters = () => {
    const currentLocale = localeSelect.value;
    const currentLevel = levelSelect.value;
    const currentTrack = trackSelect.value;
    const currentSubject = subjectSelect.value;
    const currentType = typeSelect.value;
    const hasActiveFilters = Boolean(currentLocale || currentLevel || currentTrack || currentSubject || currentType);
    let visibleCards = 0;
    let hasShownFirstMatch = false;

    cards.forEach((card) => {
      if (!hasActiveFilters) {
        card.hidden = true;
        Array.from(card.querySelectorAll("[data-teacher-delete-asset]")).forEach((row) => {
          row.hidden = true;
        });
        return;
      }

      const localeMatches = !currentLocale || card.getAttribute("data-locale") === currentLocale;
      const levelMatches = !currentLevel || card.getAttribute("data-level") === currentLevel;
      const cardTrack = card.getAttribute("data-track") || "";
      const trackMatches = !currentTrack || cardTrack.split("|").includes(currentTrack);
      const subjectMatches = !currentSubject || card.getAttribute("data-subject") === currentSubject;
      const assetRows = Array.from(card.querySelectorAll("[data-teacher-delete-asset]"));

      let hasVisibleAsset = false;

      assetRows.forEach((row) => {
        const typeMatches = !currentType || row.getAttribute("data-type") === currentType;
        const rowMatches = localeMatches && levelMatches && trackMatches && subjectMatches && typeMatches;
        const shouldShowRow = rowMatches && !hasShownFirstMatch;
        row.hidden = !shouldShowRow;
        if (shouldShowRow) {
          hasVisibleAsset = true;
          hasShownFirstMatch = true;
        }
      });

      card.hidden = !hasVisibleAsset;
      if (hasVisibleAsset) {
        visibleCards += 1;
      }
    });

    if (resultsSection) {
      resultsSection.hidden = !hasActiveFilters || visibleCards === 0;
    }

    if (emptyState) {
      emptyState.hidden = true;
    }
  };

  levelSelect.addEventListener("change", () => {
    fillTrackOptions();
    applyFilters();
  });

  trackSelect.addEventListener("change", applyFilters);
  localeSelect.addEventListener("change", applyFilters);
  subjectSelect.addEventListener("change", applyFilters);
  typeSelect.addEventListener("change", applyFilters);

  fillTrackOptions();
  applyFilters();
});

const profilePhotoForms = document.querySelectorAll("[data-profile-photo-form]");

profilePhotoForms.forEach((form) => {
  const fileInput = form.querySelector("[data-profile-photo-input]");
  const preview = form.querySelector("[data-profile-photo-preview]");
  const existingImage = form.querySelector("[data-profile-photo-image]");
  const initial = form.querySelector("[data-profile-photo-initial]");
  const zoomInput = form.querySelector("[data-profile-photo-zoom]");
  const croppedInput = form.querySelector("[data-profile-photo-cropped]");

  if (!fileInput || !preview || !zoomInput || !croppedInput) {
    return;
  }

  let activeImage = existingImage || null;
  let sourceUrl = "";
  let naturalWidth = 0;
  let naturalHeight = 0;
  let offsetX = 0;
  let offsetY = 0;
  let zoom = Number(zoomInput.value) || 1;
  let dragState = null;

  const previewSize = () => preview.clientWidth || 132;

  const clampOffsets = () => {
    if (!activeImage || !naturalWidth || !naturalHeight) {
      return;
    }

    const frame = previewSize();
    const baseScale = Math.max(frame / naturalWidth, frame / naturalHeight);
    const displayWidth = naturalWidth * baseScale * zoom;
    const displayHeight = naturalHeight * baseScale * zoom;
    const maxX = Math.max(0, (displayWidth - frame) / 2);
    const maxY = Math.max(0, (displayHeight - frame) / 2);

    offsetX = Math.min(maxX, Math.max(-maxX, offsetX));
    offsetY = Math.min(maxY, Math.max(-maxY, offsetY));
  };

  const renderImage = () => {
    if (!activeImage) {
      return;
    }

    clampOffsets();
    activeImage.style.transform = `translate(calc(-50% + ${offsetX}px), calc(-50% + ${offsetY}px)) scale(${zoom})`;
  };

  const ensureImageElement = () => {
    if (activeImage) {
      return activeImage;
    }

    const image = document.createElement("img");
    image.className = "profile-photo-field__image";
    image.setAttribute("data-profile-photo-image", "");
    preview.append(image);
    activeImage = image;
    return image;
  };

  const loadImage = (url) => {
    const image = ensureImageElement();
    sourceUrl = url;
    offsetX = 0;
    offsetY = 0;
    zoom = 1;
    zoomInput.value = "1";
    zoomInput.disabled = false;

    image.onload = () => {
      naturalWidth = image.naturalWidth || 0;
      naturalHeight = image.naturalHeight || 0;
      renderImage();
    };

    image.src = url;
    if (initial) {
      initial.hidden = true;
    }
  };

  const exportCroppedImage = () => {
    if (!activeImage || !sourceUrl || !naturalWidth || !naturalHeight) {
      croppedInput.value = "";
      return Promise.resolve();
    }

    const frame = previewSize();
    const baseScale = Math.max(frame / naturalWidth, frame / naturalHeight);
    const outputSize = 600;
    const canvas = document.createElement("canvas");
    canvas.width = outputSize;
    canvas.height = outputSize;

    const context = canvas.getContext("2d");
    if (!context) {
      croppedInput.value = "";
      return Promise.resolve();
    }

    const scaledWidth = naturalWidth * baseScale * zoom;
    const scaledHeight = naturalHeight * baseScale * zoom;
    const drawX = ((frame - scaledWidth) / 2) + offsetX;
    const drawY = ((frame - scaledHeight) / 2) + offsetY;
    const factor = outputSize / frame;

    context.drawImage(
      activeImage,
      drawX * factor,
      drawY * factor,
      scaledWidth * factor,
      scaledHeight * factor,
    );

    croppedInput.value = canvas.toDataURL("image/png", 0.92);
    return Promise.resolve();
  };

  if (activeImage && activeImage.getAttribute("src")) {
    sourceUrl = activeImage.getAttribute("src") || "";
    naturalWidth = activeImage.naturalWidth || 0;
    naturalHeight = activeImage.naturalHeight || 0;
    if (!naturalWidth || !naturalHeight) {
      activeImage.addEventListener("load", () => {
        naturalWidth = activeImage?.naturalWidth || 0;
        naturalHeight = activeImage?.naturalHeight || 0;
        renderImage();
      }, { once: true });
    } else {
      renderImage();
    }
  } else {
    zoomInput.disabled = true;
  }

  fileInput.addEventListener("change", () => {
    const file = fileInput.files?.[0];
    if (!file) {
      return;
    }

    if (sourceUrl.startsWith("blob:")) {
      URL.revokeObjectURL(sourceUrl);
    }

    loadImage(URL.createObjectURL(file));
  });

  zoomInput.addEventListener("input", () => {
    zoom = Number(zoomInput.value) || 1;
    renderImage();
  });

  preview.addEventListener("pointerdown", (event) => {
    if (!activeImage || !sourceUrl) {
      return;
    }

    dragState = {
      x: event.clientX,
      y: event.clientY,
      startX: offsetX,
      startY: offsetY,
    };

    preview.setPointerCapture(event.pointerId);
  });

  preview.addEventListener("pointermove", (event) => {
    if (!dragState) {
      return;
    }

    offsetX = dragState.startX + (event.clientX - dragState.x);
    offsetY = dragState.startY + (event.clientY - dragState.y);
    renderImage();
  });

  const stopDragging = () => {
    dragState = null;
  };

  preview.addEventListener("pointerup", stopDragging);
  preview.addEventListener("pointercancel", stopDragging);
  preview.addEventListener("pointerleave", stopDragging);

  window.addEventListener("resize", renderImage);

  form.addEventListener("submit", (event) => {
    if (!fileInput.files?.length) {
      croppedInput.value = "";
      return;
    }

    event.preventDefault();
    exportCroppedImage().then(() => {
      form.submit();
    });
  });
});

/* Course-page chatbot: zero-cost grounded assistant.
   Answers from the current lesson content (description, support, quiz).
   No API key, no network calls. */
document.querySelectorAll("[data-course-chatbot]").forEach((root) => {
  let ctx = {};
  try {
    ctx = JSON.parse(root.querySelector("[data-course-chat-context]").textContent || "{}");
  } catch {
    ctx = {};
  }
  const strings = ctx.strings || {};
  const panel = root.querySelector("[data-course-chat-panel]");
  const messages = root.querySelector("[data-course-chat-messages]");
  const form = root.querySelector("[data-course-chat-form]");
  const input = root.querySelector("[data-course-chat-input]");
  const toggles = root.querySelectorAll("[data-course-chat-toggle]");
  if (!panel || !messages || !form || !input) return;

  const norm = (s) => (s || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, " ").replace(/[^\p{L}\p{N} ]/gu, " ");
  const STOP = new Set("le la les de des du un une et est en dans que qui pour avec sur au aux ce ces il elle ils elles the and of to in a an is are was were for on with el en los las del que una por como mais ou donc or ni car je tu nous vous c est qu il elle on y pas plus tout tous toute anaa anta anti huwa hiya nahnu antum allati alladhi ma man hal".split(" "));
  const tokens = (s) => norm(s).split(/\s+/).filter((w) => w.length > 2 && !STOP.has(w));

  const sentencesOf = (text, source) => (text || "").split(/(?<=[.!?\n])\s+/).map((s) => s.trim()).filter((s) => s.length > 12).map((s) => ({ text: s, source, words: new Set(tokens(s)) }));

  const index = [
    ...sentencesOf(ctx.description, strings.source_course || ""),
    ...sentencesOf(ctx.support, strings.source_support || ""),
    ...(Array.isArray(ctx.quiz) ? ctx.quiz : []).map((q) => ({ text: q, source: strings.source_quiz || "", words: new Set(tokens(q)) })),
  ];

  const GREEK = { omega: "ω", Omega: "Ω", pi: "π", Pi: "Π", theta: "θ", alpha: "α", beta: "β", delta: "Δ", lambda: "λ", mu: "μ", sigma: "σ", phi: "φ" };
  const cleanLatex = (t) => {
    let s = t || "";
    s = s.replace(/\$\\frac\{([^}]*)\}\{([^}]*)\}\$/g, "($1)/($2)");
    s = s.replace(/\$\\text\{([^}]*)\}\$/g, "$1");
    s = s.replace(/\$\\([a-zA-Z]+)\$/g, (m, n) => GREEK[n] || n);
    s = s.replace(/\$([^$\n]+)\$/g, "$1");
    s = s.replace(/\\frac\{([^}]*)\}\{([^}]*)\}/g, "($1)/($2)");
    s = s.replace(/\\text\{([^}]*)\}/g, "$1");
    s = s.replace(/\\([a-zA-Z]+)/g, (m, n) => GREEK[n] || "");
    return s.replace(/[ \t]+/g, " ").trim();
  };

  const has = (t, ...needles) => needles.some((n) => norm(t).includes(norm(n)));

  const addMsg = (text, who, source) => {
    const div = document.createElement("div");
    div.className = "course-chat__msg course-chat__msg--" + who;
    div.textContent = text;
    if (who === "bot" && source) {
      const src = document.createElement("span");
      src.className = "course-chat__src";
      src.textContent = source;
      div.append(src);
    }
    messages.append(div);
    messages.scrollTop = messages.scrollHeight;
    return div;
  };

  const answer = (raw) => {
    const q = (raw || "").trim();
    if (!q) return null;
    if (has(q, "bonjour", "salut", "hello", "salam", "azul", "cc", "coucou")) {
      return { text: strings.welcome || "Bonjour !", source: "" };
    }
    if (has(q, "quiz", "qcm", "exercice", "exo", "questions", "test", "evaluation")) {
      if (ctx.quiz && ctx.quiz.length) {
        const shown = ctx.quiz.slice(0, 3).map((t, i) => (i + 1) + ". " + t).join("\n");
        return { text: ctx.quiz.length + " question(s):\n" + shown, source: strings.source_quiz || "" };
      }
      return { text: strings.fallback || "", source: "" };
    }
    if (has(q, "fichier", "pdf", "support", "telecharger", "download", "file", "???", "?????")) {
      return { text: ctx.hasFile ? (strings.source_support || "") + " : " + (strings.suggest_file || "") + " disponible sous la video." : (strings.fallback || ""), source: "" };
    }
    if (has(q, "premium", "prix", "payer", "abonnement", "payant", "price", "pay", "?????", "?????")) {
      return { text: strings.premium_note || "", source: "" };
    }
    if (has(q, "prof", "enseignant", "teacher", "?????", "???????")) {
      return { text: (ctx.teacher || "") + " - " + (ctx.title || ""), source: "" };
    }
    if (has(q, "resume", "resumer", "c est quoi", "cest quoi", "parle de quoi", "summar", "summary", "???", "????", "de quoi")) {
      const top = index.filter((s) => s.source !== (strings.source_quiz || "x")).slice(0, 3);
      if (top.length) return { text: top.map((s) => s.text).join(" "), source: top[0].source };
      return { text: strings.fallback || "", source: "" };
    }
    if (ctx.locked) return { text: strings.premium_note || "", source: "" };
    const qWords = new Set(tokens(q));
    if (!qWords.size) return { text: strings.fallback || "", source: "" };
    const scored = index.map((s) => {
      let score = 0;
      qWords.forEach((w) => { if (s.words.has(w)) score += 1; });
      return { s, score };
    }).filter((r) => r.score > 0).sort((a, b) => b.score - a.score).slice(0, 2);
    if (!scored.length) return { text: strings.fallback || "", source: "" };
    return { text: scored.map((r) => r.s.text).join(" "), source: scored[0].s.source };
  };

  const ask = (text) => {
    const clean = (text || "").trim();
    if (!clean) return;
    addMsg(clean, "user", "");
    const typing = addMsg("…", "bot", "");
    typing.classList.add("course-chat__msg--typing");
    const endpoint = root.getAttribute("data-endpoint") || "";
    const csrf = root.getAttribute("data-csrf") || "";
    const payload = {
      message: clean,
      title: ctx.title || "",
      teacher: ctx.teacher || "",
      description: (ctx.description || "").slice(0, 4000),
      support: (ctx.support || "").slice(0, 4000),
      quiz: Array.isArray(ctx.quiz) ? ctx.quiz.slice(0, 10) : [],
    };
    const done = (replyText, source) => {
      typing.remove();
      if (replyText) addMsg(cleanLatex(replyText), "bot", source || "");
      else {
        const res = answer(clean);
        if (res) addMsg(cleanLatex(res.text), "bot", res.source);
      }
    };
    if (!endpoint) {
      setTimeout(() => done(null), 450);
      return;
    }
    fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json", "Accept": "application/json", "X-CSRF-TOKEN": csrf },
      body: JSON.stringify(payload),
    }).then((res) => (res.ok ? res.json() : null)).then((data) => {
      done(data && data.reply ? data.reply : null);
    }).catch(() => done(null));
  };

  const setOpen = (open) => {
    panel.hidden = !open;
    toggles.forEach((b) => b.setAttribute("aria-expanded", open ? "true" : "false"));
    if (open) {
      if (!messages.children.length) addMsg(strings.welcome || "Bonjour !", "bot", "");
      input.focus();
    }
  };

  toggles.forEach((b) => b.addEventListener("click", () => setOpen(panel.hidden)));
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !panel.hidden) setOpen(false);
  });
  root.querySelectorAll("[data-course-chat-ask]").forEach((b) => b.addEventListener("click", () => {
    const kind = b.getAttribute("data-course-chat-ask");
    if (kind === "summary") ask(strings.suggest_summary || "");
    else if (kind === "quiz") ask(strings.suggest_quiz || "");
    else ask(strings.suggest_file || "");
  }));
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    ask(input.value);
    input.value = "";
  });
});
