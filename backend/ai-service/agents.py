from vector_store import query_similar

def context_agent(project_title: str, project_description: str, role_key: str) -> dict:
    return {
        "project_title": project_title,
        "project_description": project_description,
        "role_key": role_key
    }

def retrieval_agent(query_text: str, n_results: int = 3) -> str:
    try:
        results = query_similar(query_text, n_results=n_results)
        docs = results.get("documents", [[]])
        if docs and docs[0]:
            return "\n\n".join(docs[0])
    except Exception:
        pass
    return ""

def challenge_generator_agent(base_context: dict, retrieved_text: str) -> dict:
    return {
        "base_context": base_context,
        "retrieved_text": retrieved_text
    }

def evaluation_agent(project: dict, challenge: dict, submission: dict, retrieved_text: str) -> dict:
    return {
        "project": project,
        "challenge": challenge,
        "submission": submission,
        "retrieved_text": retrieved_text
    }