# utbn-backend/tools/get_transcript.py
import sys
import json
from youtube_transcript_api import YouTubeTranscriptApi, TranscriptsDisabled, NoTranscriptFound

def main():
    if len(sys.argv) < 2:
        print(json.dumps({"ok": False, "error": "MISSING_VIDEO_ID"}, ensure_ascii=False))
        return

    video_id = sys.argv[1].strip()

    try:
        # جرّب عربي ثم إنجليزي
        transcript = None
        try:
            transcript = YouTubeTranscriptApi.get_transcript(video_id, languages=["ar", "en"])
        except NoTranscriptFound:
            transcript = YouTubeTranscriptApi.get_transcript(video_id)

        text = " ".join([x.get("text", "") for x in transcript]).strip()
        if not text:
            print(json.dumps({"ok": False, "error": "EMPTY_TRANSCRIPT"}, ensure_ascii=False))
            return

        print(json.dumps({"ok": True, "text": text}, ensure_ascii=False))
    except TranscriptsDisabled:
        print(json.dumps({"ok": False, "error": "TRANSCRIPTS_DISABLED"}, ensure_ascii=False))
    except Exception as e:
        print(json.dumps({"ok": False, "error": "TRANSCRIPT_FETCH_FAILED", "detail": str(e)}, ensure_ascii=False))

if __name__ == "__main__":
    main()
