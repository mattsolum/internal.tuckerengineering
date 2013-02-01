import json
import requests

r = requests.get('http://local/internal.tuckerengineering/api/v2/client/bob_woody.json')

print json.loads(r.text);