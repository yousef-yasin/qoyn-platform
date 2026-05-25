def generate_roadmap(role_key, missing_skills):
    actions = []

    for skill in missing_skills:
        actions.append(f"Improve skill: {skill}")

    if role_key == "ml_engineer":
        actions.append("Complete Python Basics playlist")
        actions.append("Complete Machine Learning Core playlist")
        actions.append("Build one ML project with deployment")

    elif role_key == "fullstack":
        actions.append("Complete Web Development Basics playlist")
        actions.append("Build one full CRUD project")

    elif role_key == "pentester":
        actions.append("Complete Networking Basics playlist")
        actions.append("Complete Cybersecurity Fundamentals playlist")

    return {
        "missing_skills": missing_skills,
        "recommended_actions": actions
    }